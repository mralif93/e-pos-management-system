<?php

namespace App\Services;

use App\Models\OfflineSaleDraft;
use App\Models\OfflineSaleItem;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Payment;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class OfflineSaleService
{
    public function saveDraft(array $data): OfflineSaleDraft
    {
        $cartData = $data['cart_data'] ?? [];
        $items = $cartData['items'] ?? [];
        
        unset($cartData['items']);

        $draft = OfflineSaleDraft::create([
            'user_id' => $data['user_id'],
            'outlet_id' => $data['outlet_id'],
            'customer_id' => $data['customer_id'] ?? null,
            'cart_data' => $cartData,
            'total_amount' => $data['total_amount'],
            'tax_amount' => $data['tax_amount'] ?? 0,
            'discount_amount' => $data['discount_amount'] ?? 0,
            'discount_reason' => $data['discount_reason'] ?? null,
            'payments' => $data['payments'] ?? [],
            'points_earned' => $data['points_earned'] ?? 0,
            'points_redeemed' => $data['points_redeemed'] ?? 0,
            'discount_from_points' => $data['discount_from_points'] ?? 0,
            'local_created_at' => now(),
            'synced' => false,
        ]);

        foreach ($items as $item) {
            OfflineSaleItem::create([
                'draft_id' => $draft->id,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'price' => $item['price'],
                'notes' => $item['notes'] ?? null,
            ]);
        }

        return $draft;
    }

    public function syncDraft(OfflineSaleDraft $draft): Sale
    {
        return DB::transaction(function () use ($draft) {
            $sale = Sale::create([
                'outlet_id' => $draft->outlet_id,
                'user_id' => $draft->user_id,
                'customer_id' => $draft->customer_id,
                'total_amount' => $draft->total_amount,
                'tax_amount' => $draft->tax_amount,
                'discount_amount' => $draft->discount_amount,
                'discount_reason' => $draft->discount_reason,
                'status' => 'completed',
                'points_earned' => $draft->points_earned,
                'points_redeemed' => $draft->points_redeemed,
                'discount_from_points' => $draft->discount_from_points,
            ]);

            foreach ($draft->saleItems as $item) {
                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                ]);
            }

            foreach ($draft->payments as $payment) {
                Payment::create([
                    'sale_id' => $sale->id,
                    'amount' => $payment['amount'],
                    'payment_method' => $payment['payment_method'],
                ]);
            }

            $draft->markAsSynced($sale->id);

            return $sale;
        });
    }

    public function syncAllPendingDrafts(): array
    {
        $results = [
            'success' => [],
            'failed' => [],
        ];

        $pendingDrafts = OfflineSaleDraft::getUnsyncedDrafts()->get();

        foreach ($pendingDrafts as $draft) {
            try {
                $sale = $this->syncDraft($draft);
                $results['success'][] = [
                    'draft_id' => $draft->id,
                    'sale_id' => $sale->id,
                ];
            } catch (\Exception $e) {
                $results['failed'][] = [
                    'draft_id' => $draft->id,
                    'error' => $e->getMessage(),
                ];
            }
        }

        return $results;
    }

    public function getPendingCount(): int
    {
        return OfflineSaleDraft::where('synced', false)->count();
    }

    public function getPendingDrafts()
    {
        return OfflineSaleDraft::where('synced', false)
            ->with(['user', 'customer', 'saleItems.product'])
            ->orderBy('local_created_at', 'asc')
            ->get();
    }
}
