<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the admin profile.
     */
    public function index(): View
    {
        $user = auth()->user();
        
        return view('admin.profile.index', [
            'title' => 'الملف الشخصي',
            'user' => $user,
        ]);
    }

    /**
     * Update the user's profile image.
     * Any authenticated user can update their own image (no permission check needed).
     */
    public function updateImage(Request $request): RedirectResponse
    {
        $user = auth()->user();

        $request->validate([
            'image' => ['required', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ]);

        // Delete old image if exists
        if ($user->image) {
            Storage::disk('public')->delete($user->image);
        }

        // Upload new image
        $uploadPath = "users/{$user->id}";
        $imagePath = $request->file('image')->store($uploadPath, 'public');
        
        $user->update(['image' => $imagePath]);

        return redirect()->route('admin.profile')
            ->with('success', 'تم تحديث صورة البروفايل بنجاح.');
    }
}
