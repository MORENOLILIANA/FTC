<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ExpoPushService
{
    private const EXPO_PUSH_URL = 'https://exp.host/--/api/v2/push/send';

    /**
     * Envía una notificación push a uno o varios tokens de Expo.
     *
     * @param  string|array  $tokens  Token o array de tokens Expo
     * @param  string  $title
     * @param  string  $body
     * @param  array   $data   Datos extra enviados a la app
     */
    public function send(string|array $tokens, string $title, string $body, array $data = []): void
    {
        $tokens = (array) $tokens;

        // La API de Expo acepta hasta 100 mensajes por petición
        foreach (array_chunk($tokens, 100) as $chunk) {
            $messages = array_map(fn($token) => [
                'to'    => $token,
                'title' => $title,
                'body'  => $body,
                'data'  => $data,
                'sound' => 'default',
            ], $chunk);

            try {
                $response = Http::timeout(10)
                    ->withHeaders(['Accept-Encoding' => 'gzip, deflate'])
                    ->post(self::EXPO_PUSH_URL, $messages);

                if ($response->failed()) {
                    Log::warning('Expo Push: respuesta no OK', [
                        'status' => $response->status(),
                        'body'   => $response->body(),
                    ]);
                }
            } catch (\Exception $e) {
                Log::error('Expo Push: error al enviar notificación', ['error' => $e->getMessage()]);
            }
        }
    }
}
