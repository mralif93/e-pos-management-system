<?php

namespace Database\Seeders;

use App\Models\InventoryAdjustment;
use App\Models\InventoryTransfer;
use App\Models\InventoryTransferItem;
use App\Models\Outlet;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Carbon\Carbon;

class InventorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $outlets = Outlet::all();

        if ($outlets->count() < 2) {
            return; // Not enough outlets to show transfers
        }

        // 1. Create somewhat random Inventory Adjustments
        foreach ($outlets as $outlet) {
            $admin = User::where('outlet_id', $outlet->id)->where('role', 'Admin')->first();
            if (!$admin)
                $admin = User::first(); // Fallback

            $products = Product::whereHas('prices', function ($q) use ($outlet) {
                $q->where('outlet_id', $outlet->id);
            })->take(3)->get();

            foreach ($products as $product) {
                $types = ['add', 'remove', 'damage'];
                $type = $types[array_rand($types)];

                $reasons = [
                    'add' => ['Restock', 'Found in store room', 'Supplier return'],
                    'remove' => ['Expired', 'Theft', 'Used for display'],
                    'damage' => ['Damaged item in transit', 'Dropped during shelving']
                ];

                $quantityChanged = rand(1, 5);
                $quantityBefore = rand(10, 50);
                $quantityAfter = $type === 'add' ? $quantityBefore + $quantityChanged : max(0, $quantityBefore - $quantityChanged);

                InventoryAdjustment::create([
                    'outlet_id' => $outlet->id,
                    'product_id' => $product->id,
                    'user_id' => $admin->id,
                    'type' => $type,
                    'quantity_before' => $quantityBefore,
                    'quantity_after' => $quantityAfter,
                    'quantity_changed' => $quantityChanged,
                    'reason' => $reasons[$type][array_rand($reasons[$type])],
                    'reference_number' => 'ADJ-' . strtoupper(Str::random(6)),
                ]);
            }
        }

        // 2. Create some Inventory Transfers between the first two available outlets
        $fromOutlet = $outlets[0];
        $toOutlet = $outlets[1];

        $adminFrom = User::where('outlet_id', $fromOutlet->id)->where('role', 'Admin')->first() ?? User::first();
        $adminTo = User::where('outlet_id', $toOutlet->id)->where('role', 'Admin')->first() ?? User::first();

        // Pending Transfer
        $pendingTransfer = InventoryTransfer::create([
            'transfer_number' => 'TRF-' . strtoupper(Str::random(8)),
            'from_outlet_id' => $fromOutlet->id,
            'to_outlet_id' => $toOutlet->id,
            'requested_by' => $adminTo->id,
            'status' => 'pending',
            'notes' => 'Requesting extra stock for weekend sale.',
            'requested_at' => Carbon::now()->subHours(2),
        ]);

        $productForTransfer = Product::first();
        if ($productForTransfer) {
            InventoryTransferItem::create([
                'inventory_transfer_id' => $pendingTransfer->id,
                'product_id' => $productForTransfer->id,
                'quantity_requested' => 10,
                'quantity_sent' => 0,
                'quantity_received' => 0,
            ]);
        }

        // Received Transfer
        $completedTransfer = InventoryTransfer::create([
            'transfer_number' => 'TRF-' . strtoupper(Str::random(8)),
            'from_outlet_id' => $toOutlet->id,
            'to_outlet_id' => $fromOutlet->id,
            'requested_by' => $adminFrom->id,
            'approved_by' => $adminTo->id,
            'received_by' => $adminFrom->id,
            'status' => 'received',
            'notes' => 'Returned excess stock from previous month.',
            'requested_at' => Carbon::now()->subDays(3),
            'approved_at' => Carbon::now()->subDays(2),
            'received_at' => Carbon::now()->subDay(),
        ]);

        $productForTransfer2 = Product::skip(1)->first();
        if ($productForTransfer2) {
            InventoryTransferItem::create([
                'inventory_transfer_id' => $completedTransfer->id,
                'product_id' => $productForTransfer2->id,
                'quantity_requested' => 5,
                'quantity_sent' => 5,
                'quantity_received' => 5,
            ]);
        }
    }
}
