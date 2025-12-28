<?php

namespace App\Http\Controllers\Representatives\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Representatives\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LoginController extends Controller
{
    /**
     * Display the representative login view.
     */
    public function create(): View
    {
        return view('representatives.auth.login');
    }

    /**
     * Handle an incoming representative authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $representative = Auth::guard('representative')->user();

        // Verify representative is active
        if (!$representative->isActive()) {
            Auth::guard('representative')->logout();
            return back()->withErrors([
                'login' => 'حسابك غير مفعّل. يرجى التواصل مع المسؤول.',
            ]);
        }

        return redirect()->intended('/representative/dashboard');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('representative')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/representative/login');
    }
}

