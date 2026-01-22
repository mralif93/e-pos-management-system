<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Transaction;
use App\Services\TransactionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class POSController extends Controller
{
    protected TransactionService $transactionService;

    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }

    /**
     * Display the main POS interface
     */
    public function index()
    {
        $user = Auth::user();

        // Get user's outlet (or first outlet for super_admin)
        $outlet = $user->outlet ?? \App\Models\Outlet::where('is_active', true)->first();

        // Get categories
        $categories = Category::where('is_active', true)
            ->withCount('products')
            ->orderBy('name')
            ->get();

        // Get initial products (first 20)
        $products = ProductVariant::with(['product.category', 'product.brand'])
            ->whereHas('product', function ($query) {
                $query->where('is_active', true);
            })
            ->where('is_active', true)
            ->take(20)
            ->get();

        return view('pos.index', compact('outlet', 'categories', 'products', 'user'));
    }

    /**
     * Search products by name, SKU, or barcode
     */
    public function searchProducts(Request $request)
    {
        $query = $request->input('q');
        $categoryId = $request->input('category_id');

        $products = ProductVariant::with(['product.category', 'product.brand'])
            ->whereHas('product', function ($q) use ($query, $categoryId) {
                $q->where('is_active', true);

                if ($query) {
                    $q->where(function ($q2) use ($query) {
                        $q2->where('name', 'like', "%{$query}%");
                    });
                }

                if ($categoryId) {
                    $q->where('category_id', $categoryId);
                }
            })
            ->where(function ($q) use ($query) {
                if ($query) {
                    $q->where('sku', 'like', "%{$query}%")
                        ->orWhere('barcode', 'like', "%{$query}%");
                }
            })
            ->where('is_active', true)
            ->limit(50)
            ->get()
            ->map(function ($variant) {
                return [
                    'id' => $variant->id,
                    'product_id' => $variant->product_id,
                    'name' => $variant->product->name,
                    'variant_name' => $variant->variant_name,
                    'sku' => $variant->sku,
                    'barcode' => $variant->barcode,
                    'price' => $variant->base_price,
                    'image' => $variant->product->image ? asset('storage/' . $variant->product->image) : null,
                    'category' => $variant->product->category->name ?? null,
                ];
            });

        return response()->json(['data' => $products]);
    }

    /**
     * Search customer by phone number
     */
    public function searchCustomer($phone)
    {
        $customer = Customer::where('phone', $phone)->first();

        if ($customer) {
            return response()->json([
                'found' => true,
                'customer' => [
                    'id' => $customer->id,
                    'name' => $customer->name,
                    'phone' => $customer->phone,
                    'email' => $customer->email,
                    'loyalty_points' => $customer->loyalty_points,
                ],
            ]);
        }

        return response()->json(['found' => false]);
    }

    /**
     * Create new customer (quick registration)
     */
    public function createCustomer(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|unique:customers,phone',
            'email' => 'nullable|email|max:255',
        ]);

        $customer = Customer::create($validated);

        return response()->json([
            'success' => true,
            'customer' => [
                'id' => $customer->id,
                'name' => $customer->name,
                'phone' => $customer->phone,
                'email' => $customer->email,
                'loyalty_points' => $customer->loyalty_points,
            ],
        ]);
    }

    /**
     * Create transaction and process payment
     */
    public function createTransaction(Request $request)
    {
        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_variant_id' => 'required|exists:product_variants,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'customer_id' => 'nullable|exists:customers,id',
            'payment_method' => 'required|in:cash,card,duitnow',
            'payment_details' => 'nullable|array',
            'discount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $user = Auth::user();
        $outlet = $user->outlet ?? \App\Models\Outlet::where('is_active', true)->first();

        $transactionData = array_merge($validated, [
            'outlet_id' => $outlet->id,
            'user_id' => $user->id,
        ]);

        try {
            $transaction = $this->transactionService->createTransaction($transactionData);

            return response()->json([
                'success' => true,
                'transaction' => [
                    'id' => $transaction->id,
                    'invoice_number' => $transaction->invoice_number,
                    'total_amount' => $transaction->total_amount,
                    'receipt_url' => route('pos.receipt', $transaction->id),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create transaction: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Generate and display receipt
     */
    public function printReceipt(Transaction $transaction)
    {
        $transaction->load(['items.productVariant.product', 'customer', 'outlet', 'user']);

        return view('pos.receipt', compact('transaction'));
    }
}
