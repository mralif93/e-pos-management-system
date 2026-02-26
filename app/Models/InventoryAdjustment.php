<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

class InventoryAdjustment extends Model
{
    protected $fillable = [
        'product_id',
        'outlet_id',
        'user_id',
        'type',
        'quantity_before',
        'quantity_after',
        'quantity_changed',
        'reason',
        'reference_number',
    ];

    protected $casts = [
        'quantity_before' => 'integer',
        'quantity_after' => 'integer',
        'quantity_changed' => 'integer',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function outlet(): BelongsTo
    {
        return $this->belongsTo(Outlet::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function record(
        Product $product,
        int $quantityChanged,
        string $type,
        ?int $outletId = null,
        ?string $reason = null,
        ?string $referenceNumber = null,
        ?int $userId = null
    ): self {
        $quantityBefore = $product->stock_level;
        $quantityAfter = match ($type) {
            'add' => $quantityBefore + $quantityChanged,
            'remove', 'damage', 'loss' => $quantityBefore - abs($quantityChanged),
            'set' => $quantityChanged,
            'return' => $quantityBefore + abs($quantityChanged),
            'correction' => $quantityBefore + $quantityChanged,
            default => $quantityBefore,
        };

        $product->update(['stock_level' => max(0, $quantityAfter)]);

        return static::create([
            'product_id' => $product->id,
            'outlet_id' => $outletId,
            'user_id' => $userId ?? (Auth::id() ?? 1),
            'type' => $type,
            'quantity_before' => $quantityBefore,
            'quantity_after' => max(0, $quantityAfter),
            'quantity_changed' => $quantityChanged,
            'reason' => $reason,
            'reference_number' => $referenceNumber,
        ]);
    }
}
