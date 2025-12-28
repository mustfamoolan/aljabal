<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRepresentative
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->guard('representative')->check()) {
            return redirect()->route('representative.login');
        }

        $representative = auth()->guard('representative')->user();

        // Verify representative is active
        if (!$representative->isActive()) {
            auth()->guard('representative')->logout();
            return redirect()->route('representative.login')
                ->withErrors(['login' => 'حسابك غير مفعّل. يرجى التواصل مع المسؤول.']);
        }

        return $next($request);
    }
}

