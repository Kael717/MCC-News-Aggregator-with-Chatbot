<?php

namespace App\Http\Controllers;

use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class NotificationController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Get notifications for the current user.
     */
    public function index(Request $request): JsonResponse
    {
        $user = auth()->user();
        $notifications = $this->notificationService->getRecentNotifications($user, 20);
        $unreadCount = $this->notificationService->getUnreadCount($user);

        return response()->json([
            'success' => true,
            'notifications' => $notifications,
            'unread_count' => $unreadCount,
        ]);
    }

    /**
     * Mark a specific notification as read.
     */
    public function markAsRead(Request $request, int $id): JsonResponse
    {
        $user = auth()->user();
        $success = $this->notificationService->markAsRead($id, $user);

        if ($success) {
            $unreadCount = $this->notificationService->getUnreadCount($user);
            return response()->json([
                'success' => true,
                'message' => 'Notification marked as read',
                'unread_count' => $unreadCount,
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Notification not found',
        ], 404);
    }

    /**
     * Mark all notifications as read for the current user.
     */
    public function markAllAsRead(Request $request): JsonResponse
    {
        $user = auth()->user();
        $this->notificationService->markAllAsRead($user);

        return response()->json([
            'success' => true,
            'message' => 'All notifications marked as read',
            'unread_count' => 0,
        ]);
    }

    /**
     * Get unread notification count.
     */
    public function getUnreadCount(Request $request): JsonResponse
    {
        $user = auth()->user();
        $unreadCount = $this->notificationService->getUnreadCount($user);

        return response()->json([
            'unread_count' => $unreadCount,
        ]);
    }

    /**
     * Delete a specific notification.
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        $user = auth()->user();
        $notification = \App\Models\Notification::where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (!$notification) {
            return response()->json([
                'success' => false,
                'message' => 'Notification not found',
            ], 404);
        }

        $notification->delete();

        // Get updated unread count
        $unreadCount = $this->notificationService->getUnreadCount($user);

        return response()->json([
            'success' => true,
            'message' => 'Notification deleted successfully',
            'unread_count' => $unreadCount,
        ]);
    }
}
