<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Modifier extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;

    protected $fillable = ['name', 'type'];

    public function items()
    {
        return $this->hasMany(ModifierItem::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_modifiers');
    }
}
