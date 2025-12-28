<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
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
}
