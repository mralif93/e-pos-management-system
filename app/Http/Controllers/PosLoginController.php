<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class PosLoginController extends Controller
{
    public function create()
    {
        return view('pos.auth.login');
    }

    public function store(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            // Check if the authenticated user has access to POS roles
            $user = Auth::user();
            if (!in_array($user->role, ['Cashier', 'Manager', 'Admin', 'Super Admin'])) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                throw ValidationException::withMessages([
                    'email' => __('auth.failed'),
                ]);
            }

            // Check Outlet POS Access
            if ($user->outlet && !$user->outlet->has_pos_access) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()->with('error_popup', 'POS Access is temporarily disabled for this outlet. Please contact your administrator.');
            }

            // Role-based redirect
            if ($user->role === 'Cashier') {
                return redirect()->intended('/pos');
            }

            return redirect()->intended('/admin/dashboard');
        }

        throw ValidationException::withMessages([
            'email' => __('auth.failed'),
        ]);
    }

    public function destroy(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/login');
    }
}