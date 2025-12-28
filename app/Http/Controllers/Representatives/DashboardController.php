<?php

namespace App\Http\Controllers\Representatives;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the representative dashboard.
     */
    public function index(): View
    {
        $representative = auth()->guard('representative')->user();
        
        return view('representatives.dashboard.index', [
            'title' => 'لوحة التحكم - المندوبين',
            'representative' => $representative,
        ]);
    }
}

