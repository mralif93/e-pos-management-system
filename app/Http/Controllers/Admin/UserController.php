<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Outlet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('outlet');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('staff_id', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('outlet_id')) {
            $query->where('outlet_id', $request->outlet_id);
        }

        $perPage = $request->per_page ?? 10;
        $users = $query->latest()->paginate($perPage)->withQueryString();
        $outlets = Outlet::all();

        return view('admin.users.index', compact('users', 'outlets'));
    }

    public function show(User $user)
    {
        $user->load('outlet');
        $recentShifts = \App\Models\Shift::where('user_id', $user->id)
            ->with('outlet')->latest('opened_at')->limit(10)->get();
        return view('admin.users.show', compact('user', 'recentShifts'));
    }

    public function create()
    {
        $outlets = Outlet::all();
        return view('admin.users.create', compact('outlets'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|string|in:admin,manager,cashier',
            'outlet_id' => 'nullable|exists:outlets,id',
            'staff_id' => 'required|string|max:50|unique:users',
            'pin' => 'nullable|string|size:6',
            'is_active' => 'boolean',
            'theme_color' => 'nullable|string|max:20',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['is_active'] = $request->has('is_active');

        User::create($validated);

        return redirect()->route('admin.users.index')
            ->with('success', 'User created successfully.');
    }

    public function edit(User $user)
    {
        $outlets = Outlet::all();
        return view('admin.users.edit', compact('user', 'outlets'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id)
            ],
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|string|in:admin,manager,cashier',
            'outlet_id' => 'nullable|exists:outlets,id',
            'staff_id' => [
                'required',
                'string',
                'max:50',
                Rule::unique('users')->ignore($user->id)
            ],
            'pin' => 'nullable|string|size:6',
            'is_active' => 'boolean',
            'theme_color' => 'nullable|string|max:20',
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $validated['is_active'] = $request->has('is_active');

        $user->update($validated);

        return redirect()->route('admin.users.index')
            ->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'User deleted successfully.');
    }
}
