<?php

namespace App\Console\Commands;

use App\Models\DeviceToken;
use App\Models\User;
use App\Services\ExpoPushService;
use App\Services\NotificationService;
use Illuminate\Console\Command;

class SendExpiryNotifications extends Command
{
    protected $signature   = 'notifications:send-expiry';
    protected $description = 'Envía notificaciones push a usuarios con productos caducados o por caducar';

    public function __construct(
        private NotificationService $notificationService,
        private ExpoPushService     $expoPushService,
    ) {
        parent::__construct();
    }

    public function handle(): void
    {
        // Solo procesamos usuarios que tienen al menos un token registrado
        $userIds = DeviceToken::select('user_id')->distinct()->pluck('user_id');

        if ($userIds->isEmpty()) {
            $this->info('Sin tokens registrados, nada que enviar.');
            return;
        }

        $users = User::whereIn('id', $userIds)->get();

        foreach ($users as $user) {
            $notifications = $this->notificationService->getForUser($user, days: 1);

            if (empty($notifications)) {
                continue;
            }

            $tokens = DeviceToken::where('user_id', $user->id)->pluck('token')->toArray();

            // Agrupamos por tipo para enviar un mensaje por tipo, no uno por producto
            $expired      = collect($notifications)->where('type', 'expired');
            $expiringSoon = collect($notifications)->where('type', 'expiring_soon');
            $lowStock     = collect($notifications)->where('type', 'low_stock');

            if ($expired->count() > 0) {
                $names = $expired->pluck('product')->unique()->implode(', ');
                $this->expoPushService->send(
                    $tokens,
                    '🚨 Productos caducados',
                    $expired->count() === 1
                        ? "{$names} ha caducado"
                        : "{$expired->count()} productos han caducado: {$names}",
                    ['type' => 'expired']
                );
            }

            if ($expiringSoon->count() > 0) {
                $names = $expiringSoon->pluck('product')->unique()->implode(', ');
                $this->expoPushService->send(
                    $tokens,
                    '⚠️ Caducan pronto',
                    $expiringSoon->count() === 1
                        ? "{$names} caduca mañana"
                        : "{$expiringSoon->count()} productos caducan pronto: {$names}",
                    ['type' => 'expiring_soon']
                );
            }

            if ($lowStock->count() > 0) {
                $names = $lowStock->pluck('product')->unique()->implode(', ');
                $this->expoPushService->send(
                    $tokens,
                    '📦 Stock bajo',
                    $lowStock->count() === 1
                        ? "{$names} está por debajo del mínimo"
                        : "{$lowStock->count()} productos con stock bajo: {$names}",
                    ['type' => 'low_stock']
                );
            }

            $this->info("Notificaciones enviadas a {$user->email}");
        }

        $this->info('Comando completado.');
    }
}
