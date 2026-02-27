<?php

namespace Database\Seeders;

use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Payment;
use App\Models\Shift;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Outlet;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Carbon\Carbon;

class SaleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $shifts = Shift::all();
        $customers = Customer::all();

        foreach ($shifts as $shift) {
            $outlet = Outlet::find($shift->outlet_id);
            if (!$outlet)
                continue;

            // Get products available at this outlet
            $products = Product::whereHas('prices', function ($q) use ($outlet) {
                $q->where('outlet_id', $outlet->id);
            })->get();

            if ($products->isEmpty())
                continue;

            // Generate 3-5 sales per shift
            $saleCount = rand(3, 5);

            // Adjust the time based on whether the shift is active or closed
            $saleTimeBase = $shift->status === 'closed' ? Carbon::parse($shift->closed_at)->subHours(2) : Carbon::now()->subHours(1);

            for ($i = 0; $i < $saleCount; $i++) {
                $customer = rand(0, 1) ? $customers->random() : null;
                $itemCount = rand(1, 4);

                $totalAmount = 0;

                // We will create the sale first, then add items and update total
                $sale = Sale::create([
                    'outlet_id' => $shift->outlet_id,
                    'user_id' => $shift->user_id,
                    'customer_id' => $customer ? $customer->id : null,
                    'total_amount' => 0,
                    'discount_amount' => 0,
                    'tax_amount' => 0,
                    'status' => 'completed',
                    'created_at' => $saleTimeBase->copy()->addMinutes(rand(10, 120)),
                ]);

                // Create Items
                for ($j = 0; $j < $itemCount; $j++) {
                    $product = $products->random();
                    $quantity = rand(1, 3);

                    // Specific outlet price or default
                    $outletPrice = $product->prices->where('outlet_id', $outlet->id)->first();
                    $unitPrice = $outletPrice ? $outletPrice->price : $product->price;

                    $subtotal = $unitPrice * $quantity;
                    $totalAmount += $subtotal;

                    SaleItem::create([
                        'sale_id' => $sale->id,
                        'product_id' => $product->id,
                        'quantity' => $quantity,
                        'price' => $unitPrice,
                    ]);
                }

                // Apply simple 6% tax for demonstration (assuming tax exclusive)
                $taxAmount = $totalAmount * 0.06;
                $finalTotal = $totalAmount + $taxAmount;

                $sale->update([
                    'total_amount' => $finalTotal,
                    'tax_amount' => $taxAmount,
                ]);

                // Create Payment
                $paymentMethods = ['cash', 'credit_card', 'e_wallet'];
                $method = $paymentMethods[array_rand($paymentMethods)];

                Payment::create([
                    'sale_id' => $sale->id,
                    'payment_method' => $method,
                    'amount' => $finalTotal,
                ]);
            }
        }
    }
}
