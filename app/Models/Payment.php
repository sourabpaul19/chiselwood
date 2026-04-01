<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'invoice_id','amount','payment_method','payment_date', 'receipt_no','service_invoice_id','due_amount'
    ];
    // ✅ ADD THIS
    protected $casts = [
        'payment_date' => 'datetime',
        'created_at'   => 'datetime',
        'updated_at'   => 'datetime',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
    public function serviceInvoice()
    {
        return $this->belongsTo(ServiceInvoice::class);
    }
}
