<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Payment;
use App\Models\Customer;
use Illuminate\Support\Facades\DB;

class PosController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $apiToken = null;
        if ($user) {
            // Check if user has an existing token, or create a new one
            // For simplicity, we'll create a new one with a short expiration
            $apiToken = $user->createToken('pos-token', ['*'], now()->addMinutes(10))->plainTextToken;
        }

        $outletSettings = $user->outlet ? $user->outlet->settings : [];

        return view('pos.app', ['apiToken' => $apiToken, 'outletSettings' => $outletSettings]);
    }

    public function checkout()
    {
        $user = auth()->user();
        $outletSettings = $user->outlet ? $user->outlet->settings : [];

        return view('pos.checkout', ['outletSettings' => $outletSettings]);
    }

    public function searchProducts(Request $request)
    {
        $query = $request->input('query');
        $user = auth()->user();
        $userOutletId = $user ? $user->outlet_id : null;

        $products = Product::where('is_active', true)
            ->when($userOutletId, function ($queryBuilder) use ($userOutletId) {
                $queryBuilder->whereHas('prices', function ($priceQuery) use ($userOutletId) {
                    $priceQuery->where('outlet_id', $userOutletId);
                });
            })
            ->with([
                'prices' => function ($query) use ($userOutletId) { // Eager load prices for the specific outlet
                    $query->where('outlet_id', $userOutletId);
                }
            ])
            ->where(function ($queryBuilder) use ($query) {
                if (!empty($query)) {
                    $queryBuilder->where('name', 'like', '%' . $query . '%')
                        ->orWhere('slug', 'like', '%' . $query . '%');
                }
            })
            ->select('id', 'name', 'description', 'price', 'cost', 'stock_level') // Select only necessary columns from products table
            ->get();

        // Map products to include the specific price for the current outlet
        $formattedProducts = $products->map(function ($product) use ($userOutletId) {
            $outletPrice = $product->prices->firstWhere('outlet_id', $userOutletId);
            $price = $outletPrice ? $outletPrice->price : $product->price; // Use outlet price if available, else default product price
            $stockLevel = $outletPrice ? $outletPrice->stock_level : $product->stock_level; // Use outlet stock if available, else default product stock

            return [
                'id' => $product->id,
                'name' => $product->name,
                'description' => $product->description,
                'price' => $price,
                'cost' => $product->cost, // Keep default cost, as it's not per-outlet
                'stock_level' => $stockLevel,
            ];
        });

        return response()->json($formattedProducts);
    }

    public function processSale(Request $request)
    {
        $userOutletId = auth()->user()->outlet_id;

        $request->validate([
            'outlet_id' => 'required|exists:outlets,id',
            'user_id' => 'required|exists:users,id',
            'customer_id' => 'nullable|exists:customers,id',
            'total_amount' => 'required|numeric|min:0',
            'status' => 'required|string',
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'payments' => 'required|array',
            'payments.*.amount' => 'required|numeric|min:0',
            'payments.*.payment_method' => 'required|string',
        ]);

        if ($request->outlet_id != $userOutletId) {
            return response()->json(['message' => 'Unauthorized access to outlet.'], 403);
        }

        DB::beginTransaction();

        try {
            $sale = Sale::create([
                'outlet_id' => $request->outlet_id,
                'user_id' => $request->user_id,
                'customer_id' => $request->customer_id,
                'total_amount' => $request->total_amount,
                'status' => $request->status,
            ]);

            foreach ($request->items as $item) {
                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                ]);
            }

            foreach ($request->payments as $payment) {
                Payment::create([
                    'sale_id' => $sale->id,
                    'amount' => $payment['amount'],
                    'payment_method' => $payment['payment_method'],
                ]);
            }

            DB::commit();

            return response()->json(['message' => 'Sale processed successfully', 'sale' => $sale], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error processing sale', 'error' => $e->getMessage()], 500);
        }
    }
}