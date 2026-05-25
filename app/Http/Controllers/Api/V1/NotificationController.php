<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function __construct(private NotificationService $notificationService) {}

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
