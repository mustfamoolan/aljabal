<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Kreait\Firebase\Factory;
use Illuminate\Support\Facades\Log;

class ChatController extends Controller
{
    protected $auth;

    public function index()
    {
        return view('admin.chat.index', [
            'title' => 'المحادثات المباشرة'
        ]);
    }

    public function __construct()
    {
        try {
            $credentialsPath = config('firebase.projects.app.credentials');
            
            // If the path is not absolute and doesn't exist, try prepending base_path
            if (!file_exists($credentialsPath)) {
                $credentialsPath = base_path($credentialsPath);
            }

            if (!file_exists($credentialsPath)) {
                 Log::error('Firebase credentials file not found at: ' . $credentialsPath);
            }

            $factory = (new Factory)->withServiceAccount($credentialsPath);
            $this->auth = $factory->createAuth();
        } catch (\Exception $e) {
            Log::error('Firebase Initialization Error: ' . $e->getMessage());
        }
    }

    /**
     * Generate a custom Firebase token for the authenticated representative
     */
    public function getFirebaseToken(Request $request)
    {
        $representative = $request->user();
        
        if (!$representative) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        try {
            if (!$this->auth) {
                return response()->json(['message' => 'Firebase not configured'], 500);
            }

            // Prefix representative ID with 'r_' to distinguish from web users ('u_')
            $uid = 'r_' . $representative->id;
            
            $customToken = $this->auth->createCustomToken($uid, [
                'role' => 'representative',
                'name' => $representative->name,
            ]);

            return response()->json([
                'token' => $customToken->toString(),
                'uid' => $uid,
            ]);
        } catch (\Exception $e) {
            Log::error('Firebase Token Generation Error: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to generate token', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Get list of support staff (admins/employees) that the representative can message
     */
    public function getSupportStaff()
    {
        try {
            // Get all users who are not representatives (usually admins and employees)
            // We can also just get all users Since 'representative' is a separate model in this app
            $staff = User::where('is_active', true)->get();

            return response()->json([
                'staff' => $staff->map(function($user) {
                    return [
                        'id' => 'u_' . $user->id,
                        'name' => $user->name,
                        'role' => $user->roles->first()?->name ?? 'staff',
                        'avatar' => $user->image_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($user->name),
                    ];
                })
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to fetch support staff'], 500);
        }
    }

    /**
     * Send FCM notification for a new message
     */
    public function notifyNewMessage(Request $request)
    {
        $request->validate([
            'chat_id' => 'required',
            'receiver_id' => 'required', // e.g. 'r_3' or 'u_1'
            'message' => 'required',
            'sender_name' => 'required'
        ]);

        try {
            $receiverId = $request->receiver_id;
            $notificationService = new \App\Services\Notifications\NotificationService();
            
            $recipient = null;
            if (str_starts_with($receiverId, 'r_')) {
                // To representative
                $id = substr($receiverId, 2);
                $recipient = \App\Models\Representative::find($id);
            } else {
                // To web user (admin/employee)
                $id = substr($receiverId, 2);
                $recipient = \App\Models\User::find($id);
            }

            if (!$recipient) {
                return response()->json(['success' => false, 'message' => 'Recipient not found']);
            }

            $notificationService->sendChatNotification($recipient, [
                'chat_id' => $request->chat_id,
                'message' => $request->message,
                'sender_name' => $request->sender_name,
            ]);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('Chat Notification Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Get list of representatives for the admin to message
     */
    public function getRepresentatives()
    {
        try {
            $reps = \App\Models\Representative::where('is_active', true)->get();

            return response()->json([
                'representatives' => $reps->map(function($rep) {
                    return [
                        'id' => 'r_' . $rep->id,
                        'name' => $rep->name,
                        'avatar' => $rep->image_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($rep->name),
                    ];
                })
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to fetch representatives'], 500);
        }
    }

    /**
     * Generate a custom Firebase token for the authenticated web user (admin/employee)
     */
    public function getWebFirebaseToken(Request $request)
    {
        $user = auth()->user();
        
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        try {
            if (!$this->auth) {
                return response()->json(['message' => 'Firebase not configured'], 500);
            }

            // Prefix web user ID with 'u_'
            $uid = 'u_' . $user->id;
            
            $customToken = $this->auth->createCustomToken($uid, [
                'role' => $user->roles->first()?->name ?? 'staff',
                'name' => $user->name,
            ]);

            return response()->json([
                'token' => $customToken->toString(),
                'uid' => $uid,
            ]);
        } catch (\Exception $e) {
            Log::error('Firebase Web Token Generation Error: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to generate token', 'error' => $e->getMessage()], 500);
        }
    }
}
