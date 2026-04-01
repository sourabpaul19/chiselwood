<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceInvoiceItem extends Model
{
    //
    protected $fillable = [
        'service_invoice_id',
        'service_name',
        'unit_price',
        'taxable_amount',
        'gst_rate',
        'cgst',
        'sgst',
        'igst',
        'total_price'
    ];
}
