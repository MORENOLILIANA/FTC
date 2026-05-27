<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\DeviceToken;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function __construct(private NotificationService $notificationService) {}

    public function registerToken(Request $request): JsonResponse
    {
        $request->validate([
            'token'    => 'required|string',
            'platform' => 'in:android,ios',
        ]);

        DeviceToken::updateOrCreate(
            ['token' => $request->token],
            ['user_id' => $request->user()->id, 'platform' => $request->input('platform', 'android')]
        );

        return response()->json(['success' => true, 'message' => 'Token registrado correctamente']);
    }

    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'days' => 'integer|min:1|max:30',
        ]);

        $notifications = $this->notificationService->getForUser(
            $request->user(),
            $request->integer('days', 7)
        );

        return response()->json([
            'success' => true,
            'message' => 'Notifications retrieved successfully',
            'data'    => $notifications,
            'total'   => count($notifications),
        ]);
    }
}
