<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Outlet extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'phone',
        'is_active',
        'settings',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function prices()
    {
        return $this->hasMany(ProductOutletPrice::class);
    }
}
