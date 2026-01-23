<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;

class PosController extends Controller
{
    public function searchProducts(Request $request)
    {
        $query = $request->input('query');

        $products = Product::where('name', 'like', '%' . $query . '%')
                           ->orWhere('slug', 'like', '%' . $query . '%')
                           ->where('is_active', true)
                           ->get();

        return response()->json($products);
    }

    public function processSale(Request $request)
    {
        $request->validate([
            'outlet_id' => 'required|exists:outlets,id',
            'user_id' => 'required|exists:users,id',
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

        DB::beginTransaction();

        try {
            $sale = Sale::create([
                'outlet_id' => $request->outlet_id,
                'user_id' => $request->user_id,
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