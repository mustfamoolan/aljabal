<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // If representative is logged in, redirect them to their dashboard
        if (auth()->guard('representative')->check()) {
            return redirect()->route('representative.dashboard')
                ->withErrors(['message' => 'ليس لديك صلاحية للوصول إلى هذه الصفحة.']);
        }

        // If not authenticated as admin/employee, redirect to login
        if (!auth()->check()) {
            return redirect()->route('admin.login');
        }

        $user = auth()->user();

        // Verify user is admin or employee
        if (!$user->isAdmin() && !$user->isEmployee()) {
            auth()->logout();
            return redirect()->route('admin.login')
                ->withErrors(['login' => 'ليس لديك صلاحية للوصول إلى هذه الصفحة.']);
        }

        // Verify user is active
        if (!$user->isActive()) {
            auth()->logout();
            return redirect()->route('admin.login')
                ->withErrors(['login' => 'حسابك غير مفعّل. يرجى التواصل مع المسؤول.']);
        }

        return $next($request);
    }
}

