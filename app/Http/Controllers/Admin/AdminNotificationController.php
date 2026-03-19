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
            $title = $request->title;
            $body = $request->body;
            $imageUrl = $request->image_url;
            $targetType = $request->target_type;
            $targetId = $request->target_id;

            $recipients = collect();

            switch ($targetType) {
                case 'all':
                    $recipients = User::where('is_active', true)->get()
                        ->concat(Representative::where('is_active', true)->get());
                    break;
                case 'admins':
                    $recipients = User::role('admin')->where('is_active', true)->get();
                    break;
                case 'representatives':
                    $recipients = Representative::where('is_active', true)->get();
                    break;
                case 'specific_user':
                    $user = User::find($targetId);
                    if ($user) $recipients->push($user);
                    break;
                case 'specific_representative':
                    $rep = Representative::find($targetId);
                    if ($rep) $recipients->push($rep);
                    break;
            }

            if ($recipients->isEmpty()) {
                return back()->withInput()->with('error', 'لم يتم العثور على مستلمين للهدف المحدد');
            }

            foreach ($recipients as $recipient) {
                $this->notificationService->sendCustomNotification($recipient, $title, $body, [
                    'image' => $imageUrl,
                    'type' => 'custom',
                ]);
            }

            return redirect()->route('admin.notifications.send')->with('success', 'تم إرسال الإشعار بنجاح إلى ' . $recipients->count() . ' مستخدم');
        } catch (\Throwable $e) { // Catch Throwable instead of Exception to catch TypeErrors as well
            Log::error('Web Send Custom Notification Error: ' . $e->getMessage());
            return back()->withInput()->with('error', 'فشل إرسال الإشعار: ' . $e->getMessage());
        }
    }

    public function searchRecipients(Request $request)
    {
        $query = $request->query('query');
        if (!$query || strlen($query) < 2) {
            return response()->json(['data' => []]);
        }

        $users = User::where('name', 'like', "%{$query}%")
            ->orWhere('phone', 'like', "%{$query}%")
            ->limit(10)
            ->get(['id', 'name', 'phone', 'type'])
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name . ' (' . ($user->type?->value ?? 'user') . ')',
                    'type' => 'user',
                    'user_type' => $user->type?->value ?? 'user',
                ];
            });

        $reps = Representative::where('name', 'like', "%{$query}%")
            ->orWhere('phone', 'like', "%{$query}%")
            ->limit(10)
            ->get(['id', 'name', 'phone'])
            ->map(function ($rep) {
                return [
                    'id' => $rep->id,
                    'name' => $rep->name . ' (مندوب)',
                    'type' => 'representative',
                ];
            });

        return response()->json(['data' => $users->concat($reps)]);
    }
}
