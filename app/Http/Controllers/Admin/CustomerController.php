<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\LoyaltyPointTransaction;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = Customer::query();

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                    ->orWhere('phone', 'like', "%{$request->search}%")
                    ->orWhere('email', 'like', "%{$request->search}%");
            });
        }

        if ($request->tier) {
            $query->where('loyalty_tier', $request->tier);
        }

        $perPage = $request->per_page ?? 10;
        $customers = $query->orderBy('name')->paginate($perPage);
        return view('admin.customers.index', compact('customers'));
    }

    public function create()
    {
        return view('admin.customers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email',
            'address' => 'nullable|string',
        ]);

        Customer::create($request->all());
        return redirect()->route('admin.customers.index')->with('success', 'Customer created successfully');
    }

    public function show(Customer $customer)
    {
        $customer->load([
            'sales' => fn($q) => $q->where('status', '!=', 'void')->with('outlet')->latest()->limit(10),
            'loyaltyTransactions' => fn($q) => $q->latest()->limit(10),
        ]);
        $totalSpend = $customer->sales()->where('status', '!=', 'void')->sum('total_amount');
        $transactions = $customer->loyaltyTransactions;
        return view('admin.customers.show', compact('customer', 'totalSpend', 'transactions'));
    }

    public function edit($id)
    {
        $customer = Customer::findOrFail($id);
        return view('admin.customers.edit', compact('customer'));
    }

    public function update(Request $request, $id)
    {
        $customer = Customer::findOrFail($id);
        $customer->update($request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email',
        ]));

        return redirect()->route('admin.customers.index')->with('success', 'Customer updated successfully');
    }

    public function adjustPoints(Request $request, $id)
    {
        $customer = Customer::findOrFail($id);

        $request->validate([
            'points' => 'required|integer',
            'type' => 'required|in:earn,redeem,adjust',
            'description' => 'nullable|string',
        ]);

        if ($request->type === 'earn') {
            $customer->addPoints($request->points, null, $request->description);
        } elseif ($request->type === 'redeem') {
            $customer->redeemPoints($request->points, 0, null, $request->description);
        } else {
            // Adjust - add or subtract
            if ($request->points > 0) {
                $customer->addPoints($request->points, null, $request->description);
            } else {
                $customer->redeemPoints(abs($request->points), 0, null, $request->description);
            }
        }

        return response()->json(['message' => 'Points adjusted']);
    }
}
