<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Collection;

class NotificationService
{
    /**
     * Get all notifications for a user
     */
    public function getUserNotifications(User $user, int $limit = 50): Collection
    {
        return $user->notifications()
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get unread notifications for a user
     */
    public function getUnreadNotifications(User $user, int $limit = 10): Collection
    {
        return $user->unreadNotifications()
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get unread count
     */
    public function getUnreadCount(User $user): int
    {
        return $user->unreadNotifications()->count();
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(string $notificationId): bool
    {
        $notification = DatabaseNotification::findOrFail($notificationId);
        $notification->markAsRead();
        return true;
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead(User $user): int
    {
        return $user->unreadNotifications()->update(['read_at' => now()]);
    }

    /**
     * Delete notification
     */
    public function deleteNotification(string $notificationId): bool
    {
        $notification = DatabaseNotification::findOrFail($notificationId);
        return $notification->delete();
    }

    /**
     * Delete all read notifications
     */
    public function deleteReadNotifications(User $user): int
    {
        return $user->notifications()->whereNotNull('read_at')->delete();
    }
}
