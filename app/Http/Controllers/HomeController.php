<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Display the home page.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index(): View
    {
        return view('home.index', [
            'title' => 'الصفحة الرئيسية',
        ]);
    }
}

