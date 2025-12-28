<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationController extends Controller
{
    /**
     * Display a listing of notifications for the authenticated user
     */
    public function index(Request $request): View|JsonResponse
    {
        $user = auth()->user();
        $filter = $request->get('filter', 'all'); // all, read, unread

        $query = $user->notifications()->latest();

        if ($filter === 'read') {
            $query->read();
        } elseif ($filter === 'unread') {
            $query->unread();
        }

        $notifications = $query->paginate(15);

        if ($request->expectsJson()) {
            return response()->json($notifications);
        }

        $unreadCount = $user->notifications()->unread()->count();

        return view('notifications.list', compact('notifications', 'unreadCount', 'filter'));
    }

    /**
     * Display the specified notification
     */
    public function show(Notification $notification): View
    {
        // Ensure notification belongs to authenticated user
        if ($notification->user_id !== auth()->id()) {
            abort(403);
        }

        // Mark as read when viewing
        if ($notification->isUnread()) {
            $notification->markAsRead();
        }

        return view('notifications.show', compact('notification'));
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(Notification $notification): RedirectResponse|JsonResponse
    {
        // Ensure notification belongs to authenticated user
        if ($notification->user_id !== auth()->id()) {
            abort(403);
        }

        $notification->markAsRead();

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Notification marked as read',
            ]);
        }

        return back()->with('success', 'تم تحديد الإشعار كمقروء');
    }

    /**
     * Mark notification as unread
     */
    public function markAsUnread(Notification $notification): RedirectResponse|JsonResponse
    {
        // Ensure notification belongs to authenticated user
        if ($notification->user_id !== auth()->id()) {
            abort(403);
        }

        $notification->markAsUnread();

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Notification marked as unread',
            ]);
        }

        return back()->with('success', 'تم تحديد الإشعار كغير مقروء');
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead(): RedirectResponse|JsonResponse
    {
        $user = auth()->user();
        $user->notifications()->unread()->update(['read_at' => now()]);

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'All notifications marked as read',
            ]);
        }

        return back()->with('success', 'تم تحديد جميع الإشعارات كمقروءة');
    }

    /**
     * Get unread notifications count (API endpoint)
     */
    public function unreadCount(): JsonResponse
    {
        $count = auth()->user()->notifications()->unread()->count();

        return response()->json([
            'success' => true,
            'count' => $count,
        ]);
    }
}
