<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductOutlet extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'outlet_id',
        'price',
        'stock_level',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function outlet()
    {
        return $this->belongsTo(Outlet::class);
    }
}
