<?php

namespace App\Http\Controllers\Representatives;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the representative profile.
     */
    public function index(): View
    {
        $representative = auth()->guard('representative')->user();
        
        return view('representatives.profile.index', [
            'title' => 'الملف الشخصي',
            'representative' => $representative,
        ]);
    }
}

