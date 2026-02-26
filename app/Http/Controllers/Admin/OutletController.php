<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Outlet;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class OutletController extends Controller
{
    public function index(Request $request)
    {
        $query = Outlet::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('outlet_code', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $perPage = $request->per_page ?? 10;
        $outlets = $query->latest()->paginate($perPage)->withQueryString();

        return view('admin.outlets.index', compact('outlets'));
    }

    public function show(Outlet $outlet)
    {
        $outlet->load('users');
        $recentShifts = \App\Models\Shift::where('outlet_id', $outlet->id)
            ->with('user')->latest('opened_at')->limit(10)->get();
        $monthSales = \DB::table('sales')
            ->where('outlet_id', $outlet->id)
            ->where('status', '!=', 'void')
            ->whereMonth('created_at', now()->month)
            ->sum('total_amount');
        return view('admin.outlets.show', compact('outlet', 'recentShifts', 'monthSales'));
    }

    public function create()
    {
        return view('admin.outlets.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'outlet_code' => 'required|string|max:50|unique:outlets',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'is_active' => 'boolean',
            'has_pos_access' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['has_pos_access'] = $request->has('has_pos_access');

        Outlet::create($validated);

        return redirect()->route('admin.outlets.index')
            ->with('success', 'Outlet created successfully.');
    }

    public function edit(Outlet $outlet)
    {
        return view('admin.outlets.edit', compact('outlet'));
    }

    public function update(Request $request, Outlet $outlet)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'outlet_code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('outlets')->ignore($outlet->id)
            ],
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'is_active' => 'boolean',
            'has_pos_access' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['has_pos_access'] = $request->has('has_pos_access');

        $outlet->update($validated);

        return redirect()->route('admin.outlets.index')
            ->with('success', 'Outlet updated successfully.');
    }

    public function destroy(Outlet $outlet)
    {
        if ($outlet->users()->exists() || $outlet->prices()->exists()) {
            return back()->with('error', 'Cannot delete outlet because it is linked to users or product prices.');
        }

        $outlet->delete();

        return redirect()->route('admin.outlets.index')
            ->with('success', 'Outlet deleted successfully.');
    }
}
