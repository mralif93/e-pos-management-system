<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Outlet;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        return view('admin.settings.index');
    }

    public function updateSettings(Request $request)
    {
        // Settings are managed through config/env
        return response()->json(['message' => 'Settings managed through configuration files']);
    }
}
