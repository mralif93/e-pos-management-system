<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ModifierItem extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;

    protected $fillable = ['modifier_id', 'name', 'price'];

    public function modifier()
    {
        return $this->belongsTo(Modifier::class);
    }
}
