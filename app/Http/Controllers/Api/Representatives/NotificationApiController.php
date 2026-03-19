<?php

namespace App\Http\Controllers\Api\Representatives;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationApiController extends Controller
{
    /**
     * Display a listing of notifications for the authenticated representative.
     */
    public function index(Request $request): JsonResponse
    {
        $representative = auth()->user();
        $filter = $request->get('filter', 'all'); // all, read, unread
        $type = $request->get('type'); // order_status_change, financial, message, etc.

        $query = $representative->notifications()->latest();

        if ($filter === 'read') {
            $query->whereNotNull('read_at');
        } elseif ($filter === 'unread') {
            $query->whereNull('read_at');
        }

        if ($type) {
            $query->where('type', $type);
        }

        $notifications = $query->paginate(20);

        return response()->json($notifications);
    }

    /**
     * Mark notification as read.
     */
    public function markAsRead(int $id): JsonResponse
    {
        $representative = auth()->user();
        $notification = $representative->notifications()->findOrFail($id);
        
        $notification->update(['read_at' => now()]);

        return response()->json([
            'success' => true,
            'message' => 'Notification marked as read',
        ]);
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead(): JsonResponse
    {
        $representative = auth()->user();
        $representative->notifications()->whereNull('read_at')->update(['read_at' => now()]);

        return response()->json([
            'success' => true,
            'message' => 'All notifications marked as read',
        ]);
    }

    /**
     * Get unread notifications count.
     */
    public function unreadCount(): JsonResponse
    {
        $representative = auth()->user();
        $count = $representative->notifications()->whereNull('read_at')->count();

        return response()->json([
            'success' => true,
            'count' => $count,
        ]);
    }

    /**
     * Delete the specified notification.
     */
    public function destroy(int $id): JsonResponse
    {
        $representative = auth()->user();
        $notification = $representative->notifications()->findOrFail($id);
        $notification->delete();

        return response()->json([
            'success' => true,
            'message' => 'Notification deleted successfully',
        ]);
    }
}
