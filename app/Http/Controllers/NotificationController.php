<?php

namespace App\Http\Controllers;

use App\Http\Resources\NotificationResource;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function __construct(
        private NotificationService $notificationService
    ) {}

    /**
     * Get all notifications
     * GET /api/notifications
     */
    public function index(Request $request): JsonResponse
    {
        $limit = $request->input('limit', 50);
        $notifications = $this->notificationService->getUserNotifications(
            $request->user(),
            $limit
        );

        return response()->json([
            'message' => 'Daftar notifikasi berhasil dimuat.',
            'status' => 200,
            'data' => NotificationResource::collection($notifications),
            'unread_count' => $this->notificationService->getUnreadCount($request->user()),
        ]);
    }

    /**
     * Get unread notifications
     * GET /api/notifications/unread
     */
    public function unread(Request $request): JsonResponse
    {
        $limit = $request->input('limit', 10);
        $notifications = $this->notificationService->getUnreadNotifications(
            $request->user(),
            $limit
        );

        return response()->json([
            'message' => 'Notifikasi yang belum dibaca berhasil dimuat.',
            'status' => 200,
            'data' => NotificationResource::collection($notifications),
            'unread_count' => $notifications->count(),
        ]);
    }

    /**
     * Get notification statistics
     * GET /api/notifications/statistics
     */
    public function statistics(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'message' => 'Statistik notifikasi berhasil dimuat.',
            'status' => 200,
            'data' => [
                'total' => $user->notifications()->count(),
                'unread' => $user->unreadNotifications()->count(),
                'read' => $user->notifications()->whereNotNull('read_at')->count(),
            ],
        ]);
    }

    /**
     * Mark notification as read
     * POST /api/notifications/{id}/mark-as-read
     */
    public function markAsRead(string $id): JsonResponse
    {
        try {
            $this->notificationService->markAsRead($id);

            return response()->json([
                'message' => 'Notifikasi berhasil ditandai sebagai sudah dibaca.',
                'status' => 200,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal menandai notifikasi: ' . $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }

    /**
     * Mark all notifications as read
     * POST /api/notifications/mark-all-as-read
     */
    public function markAllAsRead(Request $request): JsonResponse
    {
        try {
            $count = $this->notificationService->markAllAsRead($request->user());

            return response()->json([
                'message' => "{$count} notifikasi berhasil ditandai sebagai sudah dibaca.",
                'status' => 200,
                'marked_count' => $count,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal menandai semua notifikasi: ' . $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }

    /**
     * Delete notification
     * DELETE /api/notifications/{id}
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $this->notificationService->deleteNotification($id);

            return response()->json([
                'message' => 'Notifikasi berhasil dihapus.',
                'status' => 200,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal menghapus notifikasi: ' . $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }
}
