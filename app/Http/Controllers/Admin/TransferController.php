<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InventoryTransfer;
use App\Models\InventoryTransferItem;
use App\Models\Outlet;
use App\Models\ProductOutletPrice;
use Illuminate\Http\Request;

class TransferController extends Controller
{
    public function index(Request $request)
    {
        $query = InventoryTransfer::with(['fromOutlet', 'toOutlet', 'requestedBy']);

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->outlet_id) {
            $query->where(function ($q) use ($request) {
                $q->where('from_outlet_id', $request->outlet_id)
                    ->orWhere('to_outlet_id', $request->outlet_id);
            });
        }

        $perPage = $request->per_page ?? 10;
        $transfers = $query->orderBy('requested_at', 'desc')->paginate($perPage)->withQueryString();
        $outlets = Outlet::where('is_active', true)->get();

        return view('admin.transfers.index', compact('transfers', 'outlets'));
    }

    public function create()
    {
        $outlets = Outlet::where('is_active', true)->get();
        return view('admin.transfers.create', compact('outlets'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'from_outlet' => 'required|exists:outlets,id',
            'to_outlet' => 'required|exists:outlets,id|different:from_outlet',
            'items' => 'required|array|min:1',
        ]);

        $transfer = InventoryTransfer::create([
            'from_outlet_id' => $request->from_outlet,
            'to_outlet_id' => $request->to_outlet,
            'requested_by' => auth()->user()->id,
            'status' => 'pending',
        ]);

        foreach ($request->items as $item) {
            InventoryTransferItem::create([
                'transfer_id' => $transfer->id,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
            ]);
        }

        return redirect()->route('admin.transfers.index')->with('success', 'Transfer created successfully');
    }

    public function edit(InventoryTransfer $transfer)
    {
        if ($transfer->status !== 'pending') {
            return redirect()->route('admin.transfers.index')->with('error', 'Only pending transfers can be edited.');
        }

        $outlets = Outlet::where('is_active', true)->get();
        $transfer->load('items.product');

        return view('admin.transfers.edit', compact('transfer', 'outlets'));
    }

    public function update(Request $request, InventoryTransfer $transfer)
    {
        if ($transfer->status !== 'pending') {
            return redirect()->route('admin.transfers.index')->with('error', 'Only pending transfers can be updated.');
        }

        $request->validate([
            'from_outlet' => 'required|exists:outlets,id',
            'to_outlet' => 'required|exists:outlets,id|different:from_outlet',
            'items' => 'required|array|min:1',
        ]);

        $transfer->update([
            'from_outlet_id' => $request->from_outlet,
            'to_outlet_id' => $request->to_outlet,
        ]);

        // Delete existing items
        $transfer->items()->delete();

        // Add new items
        foreach ($request->items as $item) {
            InventoryTransferItem::create([
                'transfer_id' => $transfer->id,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
            ]);
        }

        return redirect()->route('admin.transfers.index')->with('success', 'Transfer updated successfully');
    }

    public function destroy(InventoryTransfer $transfer)
    {
        if ($transfer->status !== 'pending') {
            return back()->with('error', 'Only pending transfers can be deleted.');
        }

        $transfer->items()->delete();
        $transfer->delete();

        return redirect()->route('admin.transfers.index')->with('success', 'Transfer deleted successfully.');
    }

    public function approve($id)
    {
        $transfer = InventoryTransfer::findOrFail($id);
        $transfer->approve();

        return response()->json(['message' => 'Transfer approved']);
    }

    public function reject($id)
    {
        $transfer = InventoryTransfer::findOrFail($id);
        $transfer->reject('Rejected by admin');

        return response()->json(['message' => 'Transfer rejected']);
    }

    public function transit($id)
    {
        $transfer = InventoryTransfer::findOrFail($id);
        $transfer->markInTransit();

        return response()->json(['message' => 'Transfer marked as in transit']);
    }

    public function receive($id)
    {
        $transfer = InventoryTransfer::findOrFail($id);
        $transfer->receive();

        return response()->json(['message' => 'Transfer received']);
    }

    public function show($id)
    {
        $transfer = InventoryTransfer::with(['fromOutlet', 'toOutlet', 'requestedBy', 'approvedBy', 'receivedBy', 'items.product'])
            ->findOrFail($id);
        return view('admin.transfers.show', compact('transfer'));
    }

    public function updateStatus(Request $request, $id)
    {
        $transfer = InventoryTransfer::findOrFail($id);

        $request->validate(['status' => 'required|in:approved,rejected,in_transit,received,cancelled']);

        if ($request->status === 'approved') {
            $transfer->approve();
        } elseif ($request->status === 'rejected') {
            $transfer->reject($request->reason ?? 'Rejected by admin');
        } elseif ($request->status === 'in_transit') {
            $transfer->markInTransit();
        } elseif ($request->status === 'received') {
            $transfer->receive();
        } else {
            $transfer->cancel();
        }

        return response()->json(['message' => 'Transfer updated']);
    }
}
