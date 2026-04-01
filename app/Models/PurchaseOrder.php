<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\InventoryVendor;
use App\Models\PurchaseOrderItem;

class PurchaseOrder extends Model
{
    //
    protected $fillable = [
        'po_number',
        'vendor_id',
        'order_date',
        'expected_delivery_date',
        'status',
        'subtotal',
        'tax_amount',
        'discount_amount',
        'total_amount',
        'notes',
        'created_by',      	
        'cgst',
        'sgst',
        'igst',
        'taxable_amount',
        'gst_type',

    ];

    public function vendor()
    {
        return $this->belongsTo(InventoryVendor::class);
    }
    public function items()
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

}
