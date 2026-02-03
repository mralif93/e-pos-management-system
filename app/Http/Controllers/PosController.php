<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Payment;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class PosController extends Controller
{
    public function verifyPinEndpoint(Request $request)
    {
        $request->validate(['pin' => 'required|string']);
        $user = auth()->user();

        // If user has a specific PIN, check it. 
        // If not, check if any manager PIN matches (optional, but for now strict user PIN or manager override PIN?)
        // Let's assume the user MUST have a PIN set to unlock their own session, OR use a manager PIN.
        // Simple implementation: Check against Auth user's PIN.

        if ((string) $user->pin === (string) $request->pin) {
            session(['pos_locked' => false]);
            return response()->json(['success' => true]);
        }

        return response()->json(['message' => 'Invalid PIN'], 403);
    }

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
        $apiToken = null;
        if ($user) {
            $apiToken = $user->createToken('pos-token', ['*'], now()->addMinutes(30))->plainTextToken; // Increased to 30 mins for checkout
        }
        $outletSettings = $user->outlet ? $user->outlet->settings : [];

        return view('pos.checkout', ['apiToken' => $apiToken, 'outletSettings' => $outletSettings]);
    }

    public function lock()
    {
        $user = auth()->user();
        $apiToken = null;
        if ($user) {
            $apiToken = $user->createToken('pos-token', ['*'], now()->addMinutes(120))->plainTextToken;
        }
        $outletSettings = $user->outlet ? $user->outlet->settings : [];

        session(['pos_locked' => true]);

        return view('pos.lock', ['apiToken' => $apiToken, 'outletSettings' => $outletSettings]);
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
                },
                'modifiers.items' // Eager load modifiers and their items
            ])
            ->where(function ($queryBuilder) use ($query) {
                if (!empty($query)) {
                    $queryBuilder->where('name', 'like', '%' . $query . '%')
                        ->orWhere('slug', 'like', '%' . $query . '%')
                        ->orWhere('sku', 'like', '%' . $query . '%')
                        ->orWhere('barcode', 'like', '%' . $query . '%');
                }
            })
            ->when($request->category_id, function ($q) use ($request) {
                $q->where('category_id', $request->category_id);
            })
            ->select('id', 'name', 'description', 'price', 'cost', 'stock_level', 'sku', 'barcode') // Select only necessary columns from products table
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
                'modifiers' => $product->modifiers,
            ];
        });

        return response()->json($formattedProducts);
    }

    public function verifyPin(Request $request)
    {
        $request->validate(['pin' => 'required|string']);

        $manager = User::where('pin', $request->pin)
            ->whereIn('role', ['Super Admin', 'Admin', 'Manager'])
            ->first();

        if ($manager) {
            return response()->json(['valid' => true, 'manager_name' => $manager->name]);
        }

        return response()->json(['valid' => false], 401);
    }

    public function verifyCoupon(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
            'amount' => 'required|numeric|min:0'
        ]);

        $coupon = \App\Models\Coupon::where('code', $request->code)->valid()->first();

        if (!$coupon) {
            return response()->json(['valid' => false, 'message' => 'Invalid or expired coupon.'], 404);
        }

        if (!$coupon->isValidForAmount($request->amount)) {
            return response()->json(['valid' => false, 'message' => 'Minimum spend of ' . number_format($coupon->min_spend, 2) . ' required.'], 422);
        }

        // Calculate Discount
        $discountAmount = 0;
        if ($coupon->type === 'fixed') {
            $discountAmount = $coupon->value;
        } else {
            $discountAmount = $request->amount * ($coupon->value / 100);
        }

        // Cap discount at total amount
        $discountAmount = min($discountAmount, $request->amount);

        return response()->json([
            'valid' => true,
            'coupon' => $coupon,
            'discount_amount' => $discountAmount,
            'type' => $coupon->type,
            'value' => $coupon->value
        ]);
    }

    public function processSale(Request $request)
    {
        $userOutletId = auth()->user()->outlet_id;

        $request->validate([
            'outlet_id' => 'required|exists:outlets,id',
            'user_id' => 'required|exists:users,id',
            'customer_id' => 'nullable|exists:customers,id',
            'total_amount' => 'required|numeric|min:0',
            'tax_amount' => 'required|numeric|min:0',
            'status' => 'required|string',
            'discount_amount' => 'nullable|numeric|min:0',
            'discount_reason' => 'nullable|string',
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

        $discount = $request->discount_amount ?? 0;
        $finalTotal = $request->total_amount; // Total usually includes tax, depends on logic. Assuming frontend sends breakdown.

        // Validate Total Payment
        $totalPayment = collect($request->payments)->sum('amount');
        if ($totalPayment < $finalTotal - 0.01) { // Float tolerance
            return response()->json(['message' => 'Insufficient payment amount.'], 422);
        }

        DB::beginTransaction();

        try {
            $sale = Sale::create([
                'outlet_id' => $request->outlet_id,
                'user_id' => $request->user_id,
                'customer_id' => $request->customer_id,
                'total_amount' => $request->total_amount,
                'tax_amount' => $request->tax_amount,
                'discount_amount' => $discount,
                'discount_reason' => $request->discount_reason,
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

            $sale->load(['saleItems.product', 'payments', 'customer', 'outlet', 'user']);

            return response()->json(['message' => 'Sale processed successfully', 'sale' => $sale], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error processing sale', 'error' => $e->getMessage()], 500);
        }
    }

    public function history(Request $request)
    {
        $user = auth()->user();
        $outletId = $user->outlet_id;

        $query = Sale::where('outlet_id', $outletId)
            ->with(['saleItems.product', 'user']) // Eager load
            ->latest();

        // Filter by Order ID if provided
        if ($request->filled('search')) {
            $query->where('id', 'like', '%' . $request->search . '%');
        }

        // Filter by Date if provided
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $sales = $query->paginate(20);

        return response()->json($sales);
    }

    public function voidSale(Request $request, $id)
    {
        $request->validate([
            'pin' => 'required|string', // Supervisor PIN
        ]);

        // Placeholder PIN check
        if ($request->pin !== '1234') { // Simple hardcoded PIN for now
            return response()->json(['message' => 'Invalid Supervisor PIN'], 403);
        }

        $sale = Sale::where('id', $id)->where('outlet_id', auth()->user()->outlet_id)->firstOrFail();

        if ($sale->status === 'void') {
            return response()->json(['message' => 'Sale is already voided'], 400);
        }

        $sale->update(['status' => 'void']);

        // Optional: Restore stock levels here if needed

        return response()->json(['message' => 'Sale voided successfully', 'sale' => $sale]);
    }
    public function searchCustomer(Request $request)
    {
        $query = $request->input('query');
        if (empty($query)) {
            return response()->json([]);
        }

        $customers = Customer::where('name', 'like', '%' . $query . '%')
            ->orWhere('phone', 'like', '%' . $query . '%')
            ->limit(10)
            ->get(['id', 'name', 'phone', 'email']);

        return response()->json($customers);
    }

    public function createCustomer(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20|unique:customers,phone',
            'email' => 'nullable|email|max:255|unique:customers,email',
        ]);

        $customer = Customer::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'created_by' => auth()->id(),
        ]);

        return response()->json(['message' => 'Customer created successfully', 'customer' => $customer], 201);
    }
    public function getCategories()
    {
        $user = auth()->user();
        $outletId = $user ? $user->outlet_id : null;

        $categories = Category::query()
            ->when($outletId, function ($query) use ($outletId) {
                // Return categories that have at least one active product with a price for this outlet
                $query->whereHas('products', function ($productQuery) use ($outletId) {
                    $productQuery->where('is_active', true)
                        ->whereHas('prices', function ($priceQuery) use ($outletId) {
                            $priceQuery->where('outlet_id', $outletId);
                        });
                });
            })
            ->orderBy('sort_order', 'asc')
            ->get();

        return response()->json($categories);
    }

    public function generateReceiptPdf($id)
    {
        $sale = Sale::with(['saleItems.product', 'payments', 'customer', 'user', 'outlet'])->findOrFail($id);
        $outlet = $sale->outlet ?? auth()->user()->outlet;

        $outletSettings = $outlet ? $outlet->settings : [];
        if ($outlet) {
            $outletSettings['name'] = $outlet->name;
            $outletSettings['address'] = $outlet->address;
            $outletSettings['phone'] = $outlet->phone;
        }

        $pdf = Pdf::loadView('pos.receipt-pdf', [
            'sale' => $sale,
            'outletSettings' => $outletSettings
        ]);

        // Calculate dynamic height based on content
        // Base height (Header + Footer + Meta + Totals) approx 150mm
        // Item row approx 10mm
        $itemCount = $sale->saleItems->count();
        $paymentCount = $sale->payments->count();
        $baseHeight = 120; // mm
        $itemHeight = 8; // mm per item
        $paymentHeight = 6; // mm per payment

        $totalHeight = $baseHeight + ($itemCount * $itemHeight) + ($paymentCount * $paymentHeight);

        // Convert to points (1mm = 2.83465pt)
        $msg = $outletSettings['receipt_footer'] ?? '';
        if (strlen($msg) > 50)
            $totalHeight += 10;

        $customPaper = array(0, 0, 226.77, $totalHeight * 2.83465); // 80mm width
        $pdf->setPaper($customPaper);

        return $pdf->stream('receipt-' . $sale->id . '.pdf');
    }
}