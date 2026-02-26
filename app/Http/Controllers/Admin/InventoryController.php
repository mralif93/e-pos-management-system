<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductOutletPrice;
use App\Models\InventoryAdjustment;
use App\Models\LowStockAlert;
use App\Models\Outlet;
use Illuminate\Http\Request;

class InventoryController extends controller
{
    public function index(Request $request)
    {
        $query = Product::with(['category', 'prices.outlet']);

        if ($request->search) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        if ($request->outlet_id) {
            $query->whereHas('prices', function ($q) use ($request) {
                $q->where('outlet_id', $request->outlet_id);
            });
        }

        if ($request->stock_status === 'low') {
            $query->whereRaw('stock_level <= low_stock_threshold');
        }

        $perPage = $request->per_page ?? 10;
        $products = $query->paginate($perPage)->withQueryString();
        $outlets = Outlet::where('is_active', true)->get();

        return view('admin.inventory.index', compact('products', 'outlets'));
    }

    public function adjust(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $request->validate([
            'quantity' => 'required|integer',
            'type' => 'required|in:add,remove,set,damage,loss,return,correction',
            'reason' => 'nullable|string',
            'outlet_id' => 'nullable|exists:outlets,id',
        ]);

        InventoryAdjustment::record(
            $product,
            $request->quantity,
            $request->type,
            $request->outlet_id,
            $request->reason
        );

        // Check low stock
        LowStockAlert::checkAndCreate($product, $request->outlet_id);

        return response()->json(['message' => 'Stock adjusted']);
    }

    public function adjustments()
    {
        $adjustments = InventoryAdjustment::with(['product', 'user', 'outlet'])
            ->orderBy('created_at', 'desc')
            ->paginate(30);

        return view('admin.inventory.adjustments', compact('adjustments'));
    }

    public function alerts()
    {
        $alerts = LowStockAlert::with(['product', 'outlet'])
            ->whereIn('status', ['pending', 'acknowledged'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.inventory.alerts', compact('alerts'));
    }

    public function acknowledgeAlert($id)
    {
        $alert = LowStockAlert::findOrFail($id);
        $alert->acknowledge();

        return response()->json(['message' => 'Alert acknowledged']);
    }

    public function getProductsByOutlet($outletId)
    {
        $products = ProductOutletPrice::where('outlet_id', $outletId)
            ->with('product')
            ->where('stock_level', '>', 0)
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->product->id,
                    'name' => $item->product->name,
                    'sku' => $item->product->sku,
                    'stock_level' => $item->stock_level,
                ];
            });

        return response()->json($products);
    }
}
