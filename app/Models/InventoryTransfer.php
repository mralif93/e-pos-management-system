<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class InventoryTransfer extends Model
{
    protected $fillable = [
        'transfer_number',
        'from_outlet_id',
        'to_outlet_id',
        'requested_by',
        'approved_by',
        'received_by',
        'status',
        'notes',
        'rejection_reason',
        'requested_at',
        'approved_at',
        'in_transit_at',
        'received_at',
    ];

    protected $casts = [
        'requested_at' => 'datetime',
        'approved_at' => 'datetime',
        'in_transit_at' => 'datetime',
        'received_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->transfer_number)) {
                $model->transfer_number = static::generateTransferNumber();
            }
            if (empty($model->requested_at)) {
                $model->requested_at = now();
            }
        });
    }

    public static function generateTransferNumber(): string
    {
        $date = now()->format('Ymd');
        $prefix = 'TRF-' . $date;
        
        $lastTransfer = static::where('transfer_number', 'like', $prefix . '%')
            ->orderBy('transfer_number', 'desc')
            ->first();

        $sequence = $lastTransfer ? (int) substr($lastTransfer->transfer_number, -4) + 1 : 1;
        
        return $prefix . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    public function fromOutlet(): BelongsTo
    {
        return $this->belongsTo(Outlet::class, 'from_outlet_id');
    }

    public function toOutlet(): BelongsTo
    {
        return $this->belongsTo(Outlet::class, 'to_outlet_id');
    }

    public function requestedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function receivedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(InventoryTransferItem::class);
    }

    public function approve(?int $userId = null): self
    {
        $this->update([
            'status' => 'approved',
            'approved_by' => $userId ?? Auth::id(),
            'approved_at' => now(),
        ]);

        return $this;
    }

    public function markInTransit(?int $userId = null): self
    {
        $this->update([
            'status' => 'in_transit',
            'in_transit_at' => now(),
        ]);

        foreach ($this->items as $item) {
            $this->deductFromSource($item);
        }

        return $this;
    }

    public function receive(?int $userId = null): self
    {
        $this->update([
            'status' => 'received',
            'received_by' => $userId ?? Auth::id(),
            'received_at' => now(),
        ]);

        foreach ($this->items as $item) {
            $this->addToDestination($item);
        }

        return $this;
    }

    public function reject(string $reason, ?int $userId = null): self
    {
        $this->update([
            'status' => 'rejected',
            'approved_by' => $userId ?? Auth::id(),
            'rejection_reason' => $reason,
            'approved_at' => now(),
        ]);

        return $this;
    }

    public function cancel(): self
    {
        $this->update(['status' => 'cancelled']);
        return $this;
    }

    protected function deductFromSource(InventoryTransferItem $item): void
    {
        $outletPrice = ProductOutletPrice::where('product_id', $item->product_id)
            ->where('outlet_id', $this->from_outlet_id)
            ->first();

        if ($outletPrice && $outletPrice->stock_level !== null) {
            $outletPrice->decrement('stock_level', $item->quantity_sent);
        } else {
            $item->product->decrement('stock_level', $item->quantity_sent);
        }

        InventoryAdjustment::record(
            $item->product,
            -$item->quantity_sent,
            'remove',
            $this->from_outlet_id,
            "Transfer OUT: {$this->transfer_number}",
            $this->transfer_number,
            $this->requested_by
        );
    }

    protected function addToDestination(InventoryTransferItem $item): void
    {
        $outletPrice = ProductOutletPrice::where('product_id', $item->product_id)
            ->where('outlet_id', $this->to_outlet_id)
            ->first();

        if ($outletPrice && $outletPrice->stock_level !== null) {
            $outletPrice->increment('stock_level', $item->quantity_received);
        } else {
            $item->product->increment('stock_level', $item->quantity_received);
        }

        InventoryAdjustment::record(
            $item->product,
            $item->quantity_received,
            'add',
            $this->to_outlet_id,
            "Transfer IN: {$this->transfer_number}",
            $this->transfer_number,
            $this->received_by
        );
    }
}
