<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Representative;
use App\Models\User;
use App\Services\Notifications\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AdminNotificationController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
        $this->middleware('permission:notifications.send')->only(['send', 'store']);
        $this->middleware('permission:notifications.view')->only(['searchRecipients']);
    }

    public function send()
    {
        return view('admin.notifications.send');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'image_url' => 'nullable|url',
            'target_type' => 'required|in:all,admins,representatives,specific_user,specific_representative',
            'target_id' => 'required_if:target_type,specific_user,specific_representative',
        ]);

        try {
            $this->notificationService->sendCustomNotification(
                $request->title,
                $request->body,
                $request->image_url,
                $request->target_type,
                $request->target_id
            );

            return redirect()->route('notifications.index')->with('success', 'تم إرسال الإشعار بنجاح');
        } catch (\Exception $e) {
            Log::error('Web Send Custom Notification Error: ' . $e->getMessage());
            return back()->withInput()->with('error', 'فشل إرسال الإشعار: ' . $e->getMessage());
        }
    }

    public function searchRecipients(Request $request)
    {
        $query = $request->query('query');
        if (empty($query)) return response()->json(['data' => []]);

        $users = User::where('name', 'like', "%$query%")
            ->orWhere('phone', 'like', "%$query%")
            ->limit(10)
            ->get(['id', 'name', 'phone']);

        return response()->json(['data' => $users]);
    }
}
