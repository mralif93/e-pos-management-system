<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EInvoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'sale_id',
        'lhdn_invoice_id',
        'status',
        'xml_path',
        'qr_code_path',
        'rejection_reason',
    ];

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }
}