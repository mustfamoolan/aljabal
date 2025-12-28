<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class FCMController extends Controller
{
    /**
     * Store FCM token for authenticated user
     */
    public function storeToken(Request $request): JsonResponse
    {
        Log::info('[FCM] storeToken called', [
            'has_token' => $request->has('token'),
            'token_length' => $request->has('token') ? strlen($request->token) : 0,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        $validator = Validator::make($request->all(), [
            'token' => 'required|string',
        ]);

        if ($validator->fails()) {
            Log::warning('[FCM] Validation failed', [
                'errors' => $validator->errors()->toArray(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = auth()->user();

        if (!$user) {
            Log::warning('[FCM] User not authenticated', [
                'ip' => $request->ip(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated',
            ], 401);
        }

        Log::info('[FCM] User authenticated', [
            'user_id' => $user->id,
            'user_name' => $user->name,
            'has_existing_token' => !empty($user->fcm_token),
        ]);

        $token = $request->token;
        $oldToken = $user->fcm_token;

        try {
            $user->fcm_token = $token;
            $user->save();

            Log::info('[FCM] ✅ Token saved successfully', [
                'user_id' => $user->id,
                'token_preview' => substr($token, 0, 50) . '...',
                'token_length' => strlen($token),
                'was_updated' => !empty($oldToken) && $oldToken !== $token,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Token saved successfully',
            ]);
        } catch (\Exception $e) {
            Log::error('[FCM] ❌ Error saving token', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'error_class' => get_class($e),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to save token',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove FCM token for authenticated user
     */
    public function removeToken(Request $request): JsonResponse
    {
        $user = auth()->user();

        if (!$user) {
            Log::warning('[FCM] removeToken: User not authenticated', [
                'ip' => $request->ip(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated',
            ], 401);
        }

        Log::info('[FCM] removeToken called', [
            'user_id' => $user->id,
            'user_name' => $user->name,
            'had_token' => !empty($user->fcm_token),
        ]);

        try {
            $user->fcm_token = null;
            $user->save();

            Log::info('[FCM] ✅ Token removed successfully', [
                'user_id' => $user->id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Token removed successfully',
            ]);
        } catch (\Exception $e) {
            Log::error('[FCM] ❌ Error removing token', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'error_class' => get_class($e),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to remove token',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get current user's FCM token status (debug endpoint)
     */
    public function getTokenStatus(Request $request): JsonResponse
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated',
            ], 401);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'user_id' => $user->id,
                'user_name' => $user->name,
                'has_token' => !empty($user->fcm_token),
                'token_preview' => $user->fcm_token ? substr($user->fcm_token, 0, 50) . '...' : null,
                'token_length' => $user->fcm_token ? strlen($user->fcm_token) : 0,
            ],
        ]);
    }
}
