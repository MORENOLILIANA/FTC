<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class FillNutriscore extends Command
{
    protected $signature   = 'products:fill-nutriscore
                              {--dry-run : Ver qué se actualizaría sin guardar}';
    protected $description = 'Rellena el nutriscore: real desde OFF si tiene barcode, estimado si tiene datos nutricionales';

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');

        $products = Product::whereNull('nutriscore')->get();

        if ($products->isEmpty()) {
            $this->info('✓ Todos los productos ya tienen nutriscore.');
            return self::SUCCESS;
        }

        $this->info("Productos sin nutriscore: {$products->count()}");

        $real      = 0;
        $estimated = 0;
        $skipped   = 0;

        $bar = $this->output->createProgressBar($products->count());
        $bar->start();

        foreach ($products as $product) {
            $nutriscore = null;

            // Paso 1: intentar obtener el nutriscore real desde Open Food Facts
            if (!empty($product->barcode)) {
                $nutriscore = $this->fetchNutriscoreFromOFF($product->barcode);
                if ($nutriscore) $real++;
            }

            // Paso 2: si no se encontró en OFF pero tiene datos nutricionales, estimarlo
            if (!$nutriscore && $product->calories_per_100g !== null) {
                $nutriscore = $this->estimateNutriscore($product);
                if ($nutriscore) $estimated++;
            }

            if (!$nutriscore) {
                $skipped++;
                $bar->advance();
                continue;
            }

            if ($dryRun) {
                $this->newLine();
                $tag = $real > $estimated ? '[REAL]' : '[ESTIMADO]';
                $this->line("{$tag} {$product->name}: {$nutriscore}");
            } else {
                $product->update(['nutriscore' => $nutriscore]);
            }

            $bar->advance();

            if (!empty($product->barcode)) {
                usleep(300000); // 300ms para no saturar OFF
            }
        }

        $bar->finish();
        $this->newLine(2);

        $this->table(
            ['Estado', 'Cantidad'],
            [
                ['✓ Nutriscore real (OFF)',      $real],
                ['~ Nutriscore estimado',         $estimated],
                ['✗ Sin datos suficientes',       $skipped],
            ]
        );

        return self::SUCCESS;
    }

    private function fetchNutriscoreFromOFF(string $barcode): ?string
    {
        try {
            $response = Http::timeout(8)->get(
                "https://world.openfoodfacts.org/api/v2/product/{$barcode}.json",
                ['fields' => 'nutrition_grades']
            );

            if (!$response->successful()) return null;

            $json = $response->json();
            if (($json['status'] ?? 0) !== 1) return null;

            $ns = strtoupper($json['product']['nutrition_grades'] ?? '');
            return in_array($ns, ['A', 'B', 'C', 'D', 'E']) ? $ns : null;

        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Algoritmo Nutri-Score oficial (alimentos sólidos).
     * Fuente: Santé Publique France 2023.
     */
    private function estimateNutriscore(Product $product): ?string
    {
        $cal  = (float) $product->calories_per_100g;
        $sat  = (float) $product->saturated_fat_per_100g;
        $sug  = (float) $product->sugar_per_100g;
        $salt = (float) $product->salt_per_100g;
        $fib  = (float) $product->fiber_per_100g;
        $prot = (float) $product->proteins_per_100g;

        // Necesitamos al menos calorías para estimar
        if ($cal === 0.0 && $sat === 0.0 && $sug === 0.0) return null;

        // Puntos negativos
        $n  = $this->energyPoints($cal)
            + $this->saturatedFatPoints($sat)
            + $this->sugarPoints($sug)
            + $this->sodiumPoints($salt * 400); // sal → sodio (mg)

        // Puntos positivos (sin frutas/verduras porque no tenemos ese dato)
        $p  = $this->fiberPoints($fib)
            + $this->proteinPoints($prot);

        $score = $n - $p;

        return match (true) {
            $score <= -1  => 'A',
            $score <= 2   => 'B',
            $score <= 10  => 'C',
            $score <= 18  => 'D',
            default       => 'E',
        };
    }

    private function energyPoints(float $kcal): int
    {
        return match (true) {
            $kcal <= 335  => 0, $kcal <= 670  => 1, $kcal <= 1005 => 2,
            $kcal <= 1340 => 3, $kcal <= 1675 => 4, $kcal <= 2010 => 5,
            $kcal <= 2345 => 6, $kcal <= 2680 => 7, $kcal <= 3015 => 8,
            $kcal <= 3350 => 9, default        => 10,
        };
    }

    private function saturatedFatPoints(float $g): int
    {
        return match (true) {
            $g <= 1  => 0, $g <= 2  => 1, $g <= 3  => 2,
            $g <= 4  => 3, $g <= 5  => 4, $g <= 6  => 5,
            $g <= 7  => 6, $g <= 8  => 7, $g <= 9  => 8,
            $g <= 10 => 9, default  => 10,
        };
    }

    private function sugarPoints(float $g): int
    {
        return match (true) {
            $g <= 4.5  => 0, $g <= 9   => 1, $g <= 13.5 => 2,
            $g <= 18   => 3, $g <= 22.5 => 4, $g <= 27  => 5,
            $g <= 31   => 6, $g <= 36  => 7, $g <= 40   => 8,
            $g <= 45   => 9, default    => 10,
        };
    }

    private function sodiumPoints(float $mg): int
    {
        return match (true) {
            $mg <= 90  => 0, $mg <= 180 => 1, $mg <= 270 => 2,
            $mg <= 360 => 3, $mg <= 450 => 4, $mg <= 540 => 5,
            $mg <= 630 => 6, $mg <= 720 => 7, $mg <= 810 => 8,
            $mg <= 900 => 9, default    => 10,
        };
    }

    private function fiberPoints(float $g): int
    {
        return match (true) {
            $g <= 0.9 => 0, $g <= 1.9 => 1, $g <= 2.8 => 2,
            $g <= 3.7 => 3, $g <= 4.7 => 4, default   => 5,
        };
    }

    private function proteinPoints(float $g): int
    {
        return match (true) {
            $g <= 1.6 => 0, $g <= 3.2 => 1, $g <= 4.8 => 2,
            $g <= 6.4 => 3, $g <= 8.0 => 4, default   => 5,
        };
    }
}
