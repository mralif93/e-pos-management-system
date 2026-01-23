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
    public function searchProducts(Request $request)
    {
        $query = $request->input('query');
        $user = auth()->user();
        $userOutletId = $user ? $user->outlet_id : null;

        // Debugging statements
        // dd('User ID: ' . ($user ? $user->id : 'null'), 'User Outlet ID: ' . ($userOutletId ?: 'null'));

        $products = Product::where('is_active', true)
                            ->when($userOutletId, function ($queryBuilder) use ($userOutletId) {
                                $queryBuilder->whereHas('prices', function ($priceQuery) use ($userOutletId) {
                                    $priceQuery->where('outlet_id', $userOutletId);
                                });
                            })
                           ->where(function ($queryBuilder) use ($query) {
                               $queryBuilder->where('name', 'like', '%' . $query . '%')
                                            ->orWhere('slug', 'like', '%' . $query . '%');
                           })
                           ->get();

        // Debugging statements
        // dd('Products found: ' . $products->count(), $products->toArray());

        return response()->json($products);
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