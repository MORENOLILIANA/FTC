<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class SyncNutritionalData extends Command
{
    protected $signature = 'products:sync-nutritional
                            {--limit=0 : Máximo de productos (0 = todos)}
                            {--dry-run : Ver qué se actualizaría sin guardar}';

    protected $description = 'Sincroniza datos nutricionales desde Open Food Facts';

    public function handle(): int
    {
        $limit  = (int) $this->option('limit');
        $dryRun = $this->option('dry-run');

        $query = Product::whereNotNull('barcode')
            ->where('barcode', '!=', '')
            ->whereNull('calories_per_100g');

        $total = $query->count();

        if ($total === 0) {
            $this->info('✓ No hay productos pendientes de sincronizar.');
            return self::SUCCESS;
        }

        $this->info("Productos con barcode sin nutricional: {$total}");

        if ($limit > 0) {
            $query->limit($limit);
            $this->warn("Limitado a {$limit} productos.");
        }

        $products = $query->get();

        $updated  = 0;
        $notFound = 0;
        $errors   = 0;

        $bar = $this->output->createProgressBar($products->count());
        $bar->start();

        foreach ($products as $product) {
            try {
                $data = $this->fetchFromOFF($product->barcode);

                if (!$data) {
                    $notFound++;
                } elseif ($dryRun) {
                    $this->newLine();
                    $this->line("[DRY-RUN] {$product->name} ({$product->barcode}): " . json_encode($data));
                    $updated++;
                } else {
                    $product->update($data);
                    $updated++;
                }
            } catch (\Exception $e) {
                $errors++;
                $this->newLine();
                $this->warn("Error en {$product->barcode}: " . $e->getMessage());
            }

            $bar->advance();
            usleep(300000); // 300ms entre peticiones para no saturar OFF
        }

        $bar->finish();
        $this->newLine(2);

        $this->table(
            ['Estado', 'Cantidad'],
            [
                ['✓ Actualizados',          $updated],
                ['✗ No encontrados en OFF', $notFound],
                ['! Errores',               $errors],
            ]
        );

        return self::SUCCESS;
    }

    private function fetchFromOFF(string $barcode): ?array
    {
        $response = Http::timeout(10)->get(
            "https://world.openfoodfacts.org/api/v2/product/{$barcode}.json",
            ['fields' => 'nutriments,nutrition_grades,image_front_url']
        );

        if (!$response->successful()) return null;

        $json = $response->json();

        if (($json['status'] ?? 0) !== 1 || empty($json['product'])) return null;

        $p = $json['product'];
        $n = $p['nutriments'] ?? [];

        $data = [];

        if (isset($n['energy-kcal_100g']))  $data['calories_per_100g']      = (int) round($n['energy-kcal_100g']);
        if (isset($n['proteins_100g']))      $data['proteins_per_100g']      = $n['proteins_100g'];
        if (isset($n['carbohydrates_100g'])) $data['carbs_per_100g']         = $n['carbohydrates_100g'];
        if (isset($n['fat_100g']))           $data['fats_per_100g']          = $n['fat_100g'];
        if (isset($n['saturated-fat_100g'])) $data['saturated_fat_per_100g'] = $n['saturated-fat_100g'];
        if (isset($n['fiber_100g']))         $data['fiber_per_100g']         = $n['fiber_100g'];
        if (isset($n['sugars_100g']))        $data['sugar_per_100g']         = $n['sugars_100g'];
        if (isset($n['salt_100g']))          $data['salt_per_100g']          = $n['salt_100g'];

        $ns = strtoupper($p['nutrition_grades'] ?? '');
        if (in_array($ns, ['A', 'B', 'C', 'D', 'E'])) $data['nutriscore'] = $ns;

        if (!empty($p['image_front_url'])) $data['image_url'] = $p['image_front_url'];

        return empty($data) ? null : $data;
    }
}
