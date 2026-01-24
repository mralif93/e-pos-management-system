<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'sku',
        'barcode',
        'description',
        'price',
        'cost',
        'stock_level',
        'is_active',
        'has_variants',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }



    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function prices()
    {
        return $this->hasMany(ProductOutletPrice::class);
    }

    public function modifiers()
    {
        return $this->belongsToMany(Modifier::class, 'product_modifiers');
    }

    public function saleItems()
    {
        return $this->hasMany(SaleItem::class);
    }
}
