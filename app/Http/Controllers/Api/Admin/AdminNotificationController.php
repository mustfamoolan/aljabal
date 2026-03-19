<?php
namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Representative;
use App\Models\User;
use App\Services\Notifications\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AdminNotificationController extends Controller
{
    public function __construct(
        protected NotificationService $notificationService
    ) {
    }

    /**
     * Send custom notification to users
     */
    public function sendCustomNotification(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'image_url' => 'nullable|url',
            'target_type' => 'required|string|in:all,admins,representatives,specific_user,specific_representative',
            'target_id' => 'required_if:target_type,specific_user,specific_representative',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

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
            return response()->json([
                'success' => false,
                'message' => 'No recipients found for the selected target',
            ], 404);
        }

        try {
            foreach ($recipients as $recipient) {
                $this->notificationService->sendCustomNotification($recipient, $title, $body, [
                    'image' => $imageUrl,
                    'type' => 'custom',
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Notification sent successfully to ' . $recipients->count() . ' recipients',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send notification: ' . $e->getMessage(),
            ], 500);
        }
    }
}
