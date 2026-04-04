<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class GatewaySettingsController extends Controller
{
    protected \App\Services\GatewayIntegrationService $gatewayService;

    public function __construct(\App\Services\GatewayIntegrationService $gatewayService)
    {
        $this->gatewayService = $gatewayService;
    }

    public function index()
    {
        $setting = \App\Models\GatewaySetting::first();
        return view('admin.settings.gateway', compact('setting'));
    }

    public function store(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'waseet_username' => 'required|string',
            'waseet_password' => 'required|string',
        ]);

        $result = $this->gatewayService->connect(
            $request->waseet_username,
            $request->waseet_password
        );

        if ($result['success']) {
            return back()->with('success', $result['message']);
        }

        return back()->withErrors(['error' => $result['message']]);
    }

    public function sync()
    {
        $result = $this->gatewayService->syncLocations();

        if ($result['success']) {
            return back()->with('success', $result['message']);
        }

        return back()->withErrors(['error' => $result['message']]);
    }
}
