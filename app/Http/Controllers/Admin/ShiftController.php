<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Shift;
use App\Models\Outlet;
use App\Models\User;
use App\Services\ShiftService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ShiftController extends Controller
{
    public function index(Request $request)
    {
        $query = Shift::with(['user', 'outlet', 'closedByUser']);

        if ($request->outlet_id) {
            $query->where('outlet_id', $request->outlet_id);
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->date_from) {
            $query->whereDate('created_at', '>=', Carbon::parse($request->date_from));
        }

        if ($request->date_to) {
            $query->whereDate('created_at', '<=', Carbon::parse($request->date_to));
        }

        $perPage = $request->per_page ?? 10;
        $shifts = $query->orderBy('opened_at', 'desc')->paginate($perPage)->withQueryString();

        $openShifts = Shift::with(['user', 'outlet'])->where('status', 'open')->get();
        $closedShifts = Shift::with(['user', 'outlet'])->where('status', 'closed')->orderBy('closed_at', 'desc')->limit(10)->get();

        $outlets = Outlet::where('is_active', true)->get();

        return view('admin.shifts.index', compact('shifts', 'outlets', 'openShifts', 'closedShifts'));
    }

    public function create()
    {
        $outlets = Outlet::where('is_active', true)->get();
        $users = User::all();

        return view('admin.shifts.create', compact('outlets', 'users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'outlet_id' => 'required|exists:outlets,id',
            'user_id' => 'required|exists:users,id',
            'opening_cash' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        // Check if there's already an open shift for this user and outlet
        if (Shift::where('outlet_id', $request->outlet_id)->where('user_id', $request->user_id)->where('status', 'open')->exists()) {
            return back()->with('error', 'An open shift already exists for this user at this outlet.');
        }

        Shift::create([
            'outlet_id' => $request->outlet_id,
            'user_id' => $request->user_id,
            'opening_cash' => $request->opening_cash,
            'notes' => $request->notes,
            'status' => 'open',
            'opened_at' => now(),
        ]);

        return redirect()->route('admin.shifts.index')->with('success', 'Shift created successfully.');
    }

    public function edit(Shift $shift)
    {
        $outlets = Outlet::where('is_active', true)->get();
        $users = User::all();

        return view('admin.shifts.edit', compact('shift', 'outlets', 'users'));
    }

    public function update(Request $request, Shift $shift)
    {
        $request->validate([
            'outlet_id' => 'required|exists:outlets,id',
            'user_id' => 'required|exists:users,id',
            'opening_cash' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
            'status' => 'required|in:open,closed',
        ]);

        $shift->update([
            'outlet_id' => $request->outlet_id,
            'user_id' => $request->user_id,
            'opening_cash' => $request->opening_cash,
            'notes' => $request->notes,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.shifts.index')->with('success', 'Shift updated successfully.');
    }

    public function show($id)
    {
        $shift = Shift::with([
            'user',
            'outlet',
            'closedByUser',
            'sales' => function ($q) {
                $q->where('status', '!=', 'void');
            }
        ])->findOrFail($id);

        $shiftService = new ShiftService();
        $salesSummary = $shiftService->getShiftSalesSummary($shift);

        return view('admin.shifts.show', compact('shift', 'salesSummary'));
    }

    public function destroy(Shift $shift)
    {
        if ($shift->sales()->exists()) {
            return back()->with('error', 'Cannot delete a shift that has associated sales.');
        }

        $shift->delete();

        return redirect()->route('admin.shifts.index')->with('success', 'Shift deleted successfully.');
    }

    public function close(Request $request, Shift $shift)
    {
        if ($shift->status !== 'open') {
            return back()->with('error', 'Shift is already closed.');
        }

        $request->validate([
            'closing_cash' => 'required|numeric|min:0',
        ]);

        $shiftService = new ShiftService();
        $salesSummary = $shiftService->getShiftSalesSummary($shift);

        $expectedCash = $shift->calculateExpectedCash((float) ($salesSummary['cash_total'] ?? 0), (float) $shift->opening_cash);

        $shift->close([
            'closing_cash' => $request->closing_cash,
            'expected_cash' => $expectedCash,
            'card_total' => $salesSummary['total_card_sales'] ?? 0,
            'other_total' => $salesSummary['total_other_sales'] ?? 0,
            'total_sales' => $salesSummary['total_sales'] ?? 0,
            'transaction_count' => $salesSummary['transaction_count'] ?? 0,
            'notes' => $request->notes,
            'closed_by' => auth()->id(),
        ]);

        return back()->with('success', 'Shift closed successfully.');
    }
}
