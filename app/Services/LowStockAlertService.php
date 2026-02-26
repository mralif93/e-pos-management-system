<?php

namespace App\Services;

use App\Models\LowStockAlert;
use App\Models\Product;
use App\Models\ProductOutletPrice;
use Illuminate\Support\Collection;

class LowStockAlertService
{
    public function checkGlobalStock(): Collection
    {
        $alerts = collect();
        
        $products = Product::where('is_active', true)
            ->whereNotNull('stock_level')
            ->whereColumn('stock_level', '<=', 'low_stock_threshold')
            ->get();

        foreach ($products as $product) {
            $alert = LowStockAlert::checkAndCreate($product, null);
            if ($alert) {
                $alerts->push($alert);
            }
        }

        return $alerts;
    }

    public function checkOutletStock(int $outletId): Collection
    {
        $alerts = collect();
        
        $outletPrices = ProductOutletPrice::where('outlet_id', $outletId)
            ->whereNotNull('stock_level')
            ->whereColumn('stock_level', '<=', 'low_stock_threshold')
            ->with('product')
            ->get();

        foreach ($outletPrices as $outletPrice) {
            $product = $outletPrice->product;
            $alert = LowStockAlert::checkAndCreate($product, $outletId);
            if ($alert) {
                $alerts->push($alert);
            }
        }

        return $alerts;
    }

    public function getPendingAlerts(?int $outletId = null): Collection
    {
        return LowStockAlert::where('status', 'pending')
            ->when($outletId, fn($q) => $q->where('outlet_id', $outletId))
            ->with(['product', 'outlet'])
            ->orderBy('current_stock', 'asc')
            ->get();
    }

    public function getAllActiveAlerts(): Collection
    {
        return LowStockAlert::whereIn('status', ['pending', 'acknowledged'])
            ->with(['product', 'outlet', 'acknowledgedBy'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function acknowledgeAlert(int $alertId, ?int $userId = null): LowStockAlert
    {
        $alert = LowStockAlert::findOrFail($alertId);
        $alert->acknowledge($userId);
        
        return $alert;
    }

    public function getAlertStats(): array
    {
        return [
            'total' => LowStockAlert::whereIn('status', ['pending', 'acknowledged'])->count(),
            'pending' => LowStockAlert::where('status', 'pending')->count(),
            'acknowledged' => LowStockAlert::where('status', 'acknowledged')->count(),
            'resolved' => LowStockAlert::where('status', 'resolved')->count(),
        ];
    }
}
