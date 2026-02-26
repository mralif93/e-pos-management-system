<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OfflineSaleItem extends Model
{
    protected $fillable = [
        'draft_id',
        'product_id',
        'quantity',
        'price',
        'notes',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'price' => 'decimal:2',
    ];

    public function draft(): BelongsTo
    {
        return $this->belongsTo(OfflineSaleDraft::class, 'draft_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
