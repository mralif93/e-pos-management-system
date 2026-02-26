<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductOutletPrice;
use App\Models\Outlet;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['category', 'prices.outlet']);

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                    ->orWhere('sku', 'like', "%{$request->search}%");
            });
        }

        if ($request->outlet_id) {
            $query->whereHas('prices', function ($q) use ($request) {
                $q->where('outlet_id', $request->outlet_id);
            });
        }

        if ($request->category_id) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->status) {
            $query->where('is_active', $request->status === 'active');
        }

        $perPage = $request->per_page ?? 10;
        $products = $query->orderBy('name')->paginate($perPage)->withQueryString();
        $categories = Category::orderBy('name')->get();
        $outlets = Outlet::where('is_active', true)->get();

        return view('admin.products.index', compact('products', 'categories', 'outlets'));
    }

    public function show(Product $product)
    {
        $product->load(['category', 'prices.outlet', 'variants']);
        $recentSales = \DB::table('sale_items')
            ->join('sales', 'sales.id', '=', 'sale_items.sale_id')
            ->join('outlets', 'outlets.id', '=', 'sales.outlet_id')
            ->where('sale_items.product_id', $product->id)
            ->where('sales.status', '!=', 'void')
            ->select('sale_items.*', 'sales.created_at as sale_date', 'outlets.name as outlet_name')
            ->orderByDesc('sales.created_at')
            ->limit(10)
            ->get();
        $totalSold = \DB::table('sale_items')
            ->join('sales', 'sales.id', '=', 'sale_items.sale_id')
            ->where('sale_items.product_id', $product->id)
            ->where('sales.status', '!=', 'void')
            ->sum('sale_items.quantity');
        return view('admin.products.show', compact('product', 'recentSales', 'totalSold'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();
        $outlets = Outlet::where('is_active', true)->get();
        return view('admin.products.create', compact('categories', 'outlets'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|string|unique:products,sku',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'stock_level' => 'required|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $product = Product::create([
            'name' => $request->name,
            'sku' => $request->sku,
            'category_id' => $request->category_id,
            'price' => $request->price,
            'stock_level' => $request->stock_level,
            'is_active' => $request->is_active ?? true,
            'low_stock_threshold' => $request->low_stock_threshold ?? 10,
        ]);

        // Create outlet prices for all active outlets
        $outlets = Outlet::where('is_active', true)->get();
        foreach ($outlets as $outlet) {
            ProductOutletPrice::updateOrCreate(
                ['product_id' => $product->id, 'outlet_id' => $outlet->id],
                ['price' => $request->price, 'stock_level' => $request->stock_level]
            );
        }

        return redirect()->route('admin.products.index')->with('success', 'Product created successfully');
    }

    public function edit($id)
    {
        $product = Product::with(['category', 'prices.outlet'])->findOrFail($id);
        $categories = Category::orderBy('name')->get();
        $outlets = Outlet::where('is_active', true)->get();
        return view('admin.products.edit', compact('product', 'categories', 'outlets'));
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|string|unique:products,sku,' . $id,
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'stock_level' => 'required|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $product->update([
            'name' => $request->name,
            'sku' => $request->sku,
            'category_id' => $request->category_id,
            'price' => $request->price,
            'stock_level' => $request->stock_level,
            'is_active' => $request->is_active ?? true,
            'low_stock_threshold' => $request->low_stock_threshold ?? 10,
        ]);

        return redirect()->route('admin.products.index')->with('success', 'Product updated successfully');
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();

        return response()->json(['message' => 'Product deleted successfully']);
    }

    public function updateStock(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $request->validate([
            'stock_level' => 'required|integer|min:0',
            'outlet_id' => 'nullable|exists:outlets,id',
            'reason' => 'nullable|string',
        ]);

        if ($request->outlet_id) {
            $outletPrice = ProductOutletPrice::where('product_id', $product->id)
                ->where('outlet_id', $request->outlet_id)
                ->first();

            if ($outletPrice) {
                $outletPrice->update(['stock_level' => $request->stock_level]);
            }
        } else {
            $product->update(['stock_level' => $request->stock_level]);
        }

        return response()->json(['message' => 'Stock updated successfully']);
    }
}
