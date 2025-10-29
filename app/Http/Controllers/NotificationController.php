<?php

namespace App\Http\Controllers;

use App\Http\Resources\NotificationResource;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class NotificationController extends Controller
{
    public function __construct(
        private NotificationService $notificationService
    ) {}

    /**
     * Get all notifications
     * GET /api/notifications
     */
    #[OA\Get(
        path: '/notifications',
        summary: 'Get All Notifications',
        description: 'Retrieve all notifications for the authenticated user with pagination support',
        security: [['sanctum' => []]],
        tags: ['Notifications'],
        parameters: [
            new OA\Parameter(
                name: 'limit',
                description: 'Maximum number of notifications to return',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'integer', default: 50, example: 20)
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Notifications retrieved successfully',
                content: new OA\JsonContent(ref: '#/components/schemas/NotificationListResponse')
            ),
            new OA\Response(
                response: 401,
                description: 'Unauthorized',
                content: new OA\JsonContent(ref: '#/components/schemas/UnauthorizedResponse')
            ),
        ]
    )]
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
    #[OA\Get(
        path: '/notifications/unread',
        summary: 'Get Unread Notifications',
        description: 'Retrieve only unread notifications for the authenticated user',
        security: [['sanctum' => []]],
        tags: ['Notifications'],
        parameters: [
            new OA\Parameter(
                name: 'limit',
                description: 'Maximum number of unread notifications to return',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'integer', default: 10, example: 10)
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Unread notifications retrieved successfully',
                content: new OA\JsonContent(ref: '#/components/schemas/NotificationListResponse')
            ),
            new OA\Response(
                response: 401,
                description: 'Unauthorized',
                content: new OA\JsonContent(ref: '#/components/schemas/UnauthorizedResponse')
            ),
        ]
    )]
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
    #[OA\Get(
        path: '/notifications/statistics',
        summary: 'Get Notification Statistics',
        description: 'Get statistics about user notifications including total, read, and unread counts',
        security: [['sanctum' => []]],
        tags: ['Notifications'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Notification statistics retrieved successfully',
                content: new OA\JsonContent(ref: '#/components/schemas/NotificationStatisticsResponse')
            ),
            new OA\Response(
                response: 401,
                description: 'Unauthorized',
                content: new OA\JsonContent(ref: '#/components/schemas/UnauthorizedResponse')
            ),
        ]
    )]
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
    #[OA\Post(
        path: '/notifications/{id}/mark-as-read',
        summary: 'Mark Notification as Read',
        description: 'Mark a specific notification as read',
        security: [['sanctum' => []]],
        tags: ['Notifications'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'Notification ID (UUID)',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'string', example: '9d3e461f-5a7c-4c5e-8b5d-7f9c8d5e6a4b')
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Notification marked as read successfully',
                content: new OA\JsonContent(ref: '#/components/schemas/MarkNotificationResponse')
            ),
            new OA\Response(
                response: 401,
                description: 'Unauthorized',
                content: new OA\JsonContent(ref: '#/components/schemas/UnauthorizedResponse')
            ),
            new OA\Response(
                response: 500,
                description: 'Server error',
                content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')
            ),
        ]
    )]
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
    #[OA\Post(
        path: '/notifications/mark-all-as-read',
        summary: 'Mark All Notifications as Read',
        description: 'Mark all notifications for the authenticated user as read',
        security: [['sanctum' => []]],
        tags: ['Notifications'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'All notifications marked as read successfully',
                content: new OA\JsonContent(ref: '#/components/schemas/MarkAllNotificationResponse')
            ),
            new OA\Response(
                response: 401,
                description: 'Unauthorized',
                content: new OA\JsonContent(ref: '#/components/schemas/UnauthorizedResponse')
            ),
            new OA\Response(
                response: 500,
                description: 'Server error',
                content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')
            ),
        ]
    )]
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
    #[OA\Delete(
        path: '/notifications/{id}',
        summary: 'Delete Notification',
        description: 'Delete a specific notification permanently',
        security: [['sanctum' => []]],
        tags: ['Notifications'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'Notification ID (UUID)',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'string', example: '9d3e461f-5a7c-4c5e-8b5d-7f9c8d5e6a4b')
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Notification deleted successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Notifikasi berhasil dihapus.'),
                        new OA\Property(property: 'status', type: 'integer', example: 200),
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: 'Unauthorized',
                content: new OA\JsonContent(ref: '#/components/schemas/UnauthorizedResponse')
            ),
            new OA\Response(
                response: 500,
                description: 'Server error',
                content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')
            ),
        ]
    )]
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
