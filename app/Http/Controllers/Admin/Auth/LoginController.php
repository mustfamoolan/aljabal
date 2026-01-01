<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Enums\UserType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LoginController extends Controller
{
    /**
     * Display the admin login view.
     */
    public function create(): View
    {
        return view('admin.auth.login');
    }

    /**
     * Handle an incoming admin authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = Auth::user();

        // Verify user is admin or employee
        if (!$user->isAdmin() && !$user->isEmployee()) {
            Auth::logout();
            return back()->withErrors([
                'login' => 'ليس لديك صلاحية للوصول إلى هذه الصفحة.',
            ]);
        }

        // Verify user is active
        if (!$user->isActive()) {
            Auth::logout();
            return back()->withErrors([
                'login' => 'حسابك غير مفعّل. يرجى التواصل مع المسؤول.',
            ]);
        }

        // Clear any intended URL to avoid redirecting to logout or other unwanted pages
        $request->session()->forget('url.intended');
        
        return redirect()->route('home.index');
    }
}
