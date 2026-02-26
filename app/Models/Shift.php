<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Shift extends Model
{
    protected $fillable = [
        'outlet_id',
        'user_id',
        'shift_number',
        'opening_cash',
        'closing_cash',
        'expected_cash',
        'cash_difference',
        'card_total',
        'other_total',
        'total_sales',
        'transaction_count',
        'status',
        'notes',
        'opened_at',
        'closed_at',
        'closed_by',
    ];

    protected $casts = [
        'opening_cash' => 'decimal:2',
        'closing_cash' => 'decimal:2',
        'expected_cash' => 'decimal:2',
        'cash_difference' => 'decimal:2',
        'card_total' => 'decimal:2',
        'other_total' => 'decimal:2',
        'total_sales' => 'decimal:2',
        'transaction_count' => 'integer',
        'opened_at' => 'datetime',
        'closed_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->shift_number)) {
                $model->shift_number = static::generateShiftNumber($model->outlet_id);
            }
            if (empty($model->opened_at)) {
                $model->opened_at = now();
            }
        });
    }

    public static function generateShiftNumber(int $outletId): string
    {
        $date = now()->format('Ymd');
        $prefix = 'SH' . $outletId . $date;
        
        $lastShift = static::where('shift_number', 'like', $prefix . '%')
            ->orderBy('shift_number', 'desc')
            ->first();

        $sequence = $lastShift ? (int) substr($lastShift->shift_number, -3) + 1 : 1;
        
        return $prefix . str_pad($sequence, 3, '0', STR_PAD_LEFT);
    }

    public function outlet(): BelongsTo
    {
        return $this->belongsTo(Outlet::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function closedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'closed_by');
    }

    public static function getOpenShift(int $outletId, int $userId): ?Shift
    {
        return static::where('outlet_id', $outletId)
            ->where('user_id', $userId)
            ->where('status', 'open')
            ->first();
    }

    public function close(array $data): self
    {
        $this->update([
            'closing_cash' => $data['closing_cash'],
            'expected_cash' => $data['expected_cash'],
            'cash_difference' => $data['closing_cash'] - $data['expected_cash'],
            'card_total' => $data['card_total'] ?? 0,
            'other_total' => $data['other_total'] ?? 0,
            'total_sales' => $data['total_sales'] ?? 0,
            'transaction_count' => $data['transaction_count'] ?? 0,
            'status' => 'closed',
            'notes' => $data['notes'] ?? null,
            'closed_at' => now(),
            'closed_by' => $data['closed_by'] ?? (auth()->id() ?? null),
        ]);

        return $this;
    }

    public function calculateExpectedCash(float $salesAmount, float $openingCash): float
    {
        return $openingCash + $salesAmount;
    }
}
