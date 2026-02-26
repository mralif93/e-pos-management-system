<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class LowStockAlert extends Model
{
    protected $fillable = [
        'product_id',
        'outlet_id',
        'current_stock',
        'threshold',
        'status',
        'acknowledged_by',
        'acknowledged_at',
    ];

    protected $casts = [
        'current_stock' => 'integer',
        'threshold' => 'integer',
        'acknowledged_at' => 'datetime',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function outlet(): BelongsTo
    {
        return $this->belongsTo(Outlet::class);
    }

    public function acknowledgedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'acknowledged_by');
    }

    public static function checkAndCreate(Product $product, ?int $outletId = null): ?self
    {
        $stockLevel = $product->stock_level;
        $threshold = $product->low_stock_threshold ?? config('services.inventory.low_stock_threshold_default', 10);

        if ($stockLevel > $threshold) {
            static::where('product_id', $product->id)
                ->where('outlet_id', $outletId)
                ->where('status', 'pending')
                ->update(['status' => 'resolved']);
            
            return null;
        }

        $existing = static::where('product_id', $product->id)
            ->where('outlet_id', $outletId)
            ->where('status', 'pending')
            ->first();

        if ($existing) {
            $existing->update(['current_stock' => $stockLevel]);
            return $existing;
        }

        return static::create([
            'product_id' => $product->id,
            'outlet_id' => $outletId,
            'current_stock' => $stockLevel,
            'threshold' => $threshold,
            'status' => 'pending',
        ]);
    }

    public function acknowledge(?int $userId = null): void
    {
        $this->update([
            'status' => 'acknowledged',
            'acknowledged_by' => $userId ?? Auth::id(),
            'acknowledged_at' => Carbon::now(),
        ]);
    }
}
