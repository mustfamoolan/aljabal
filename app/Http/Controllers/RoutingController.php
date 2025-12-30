<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class RoutingController extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function __construct()
    {
        // $this->
        // middleware('auth')->
        // except('index');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function index(Request $request)
    {
        if (Auth::user()) {
            return redirect('/dashboards/index');
        } else {
            return redirect()->route('admin.login');
        }
    }

    /**
     * Display a view based on first route param
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function root(Request $request, $first)
    {
        // Exclude representative routes - they have their own controllers
        if ($first === 'representative') {
            abort(404);
        }

        // Exclude .well-known and other system paths
        if ($first === '.well-known' || str_starts_with($first, '.')) {
            abort(404);
        }

        // Exclude static files and API routes
        $excludedExtensions = ['js', 'css', 'json', 'xml', 'txt', 'ico', 'png', 'jpg', 'jpeg', 'gif', 'svg', 'woff', 'woff2', 'ttf', 'eot'];
        $extension = pathinfo($first, PATHINFO_EXTENSION);

        if (in_array(strtolower($extension), $excludedExtensions)) {
            abort(404);
        }

        return view($first);
    }

    /**
     * second level route
     */
    public function secondLevel(Request $request, $first, $second)
    {
        // Exclude representative routes - they have their own controllers
        if ($first === 'representative') {
            abort(404);
        }

        // Exclude .well-known and other system paths
        if ($first === '.well-known' || str_starts_with($first, '.')) {
            abort(404);
        }

        // Exclude static files and API routes
        $excludedExtensions = ['js', 'css', 'json', 'xml', 'txt', 'ico', 'png', 'jpg', 'jpeg', 'gif', 'svg', 'woff', 'woff2', 'ttf', 'eot'];
        $extension = pathinfo($second, PATHINFO_EXTENSION);
        if (in_array(strtolower($extension), $excludedExtensions)) {
            abort(404);
        }

        // Redirect known routes to their controllers
        if ($first === 'users' && $second === 'pages-permission') {
            return redirect()->route('users.pages-permission');
        }

        // Exclude admin/profile from catch-all routing (handled by direct route)
        if ($first === 'admin' && $second === 'profile') {
            abort(404);
        }

        return view($first . '.' . $second);
    }

    /**
     * third level route
     */
    public function thirdLevel(Request $request, $first, $second, $third)
    {
        // Exclude representative routes - they have their own controllers
        if ($first === 'representative') {
            abort(404);
        }

        // Exclude .well-known and other system paths
        if ($first === '.well-known' || str_starts_with($first, '.')) {
            abort(404);
        }

        // Exclude static files and API routes
        $excludedExtensions = ['js', 'css', 'json', 'xml', 'txt', 'ico', 'png', 'jpg', 'jpeg', 'gif', 'svg', 'woff', 'woff2', 'ttf', 'eot'];
        $extension = pathinfo($third, PATHINFO_EXTENSION);
        if (in_array(strtolower($extension), $excludedExtensions)) {
            abort(404);
        }

        // Redirect known routes to their controllers
        if ($first === 'users' && $second === 'role' && $third === 'list') {
            return redirect()->route('users.role.list');
        }
        if ($first === 'users' && $second === 'role' && $third === 'create') {
            return redirect()->route('users.role.create');
        }
        if ($first === 'users' && $second === 'permissions' && $third === 'create') {
            return redirect()->route('users.permissions.create');
        }
        if ($first === 'users' && $second === 'users' && $third === 'list') {
            return redirect()->route('users.users.list');
        }

        return view($first . '.' . $second . '.' . $third);
    }
}
