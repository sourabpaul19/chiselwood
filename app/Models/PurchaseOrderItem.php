<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\InventoryItem;
use App\Models\PurchaseOrder;

class PurchaseOrderItem extends Model
{
    //
    protected $fillable = [
        'purchase_order_id',
        'inventory_item_id',
        'quantity',
        'unit_price',
        'tax_percent',
        'discount',
        'total',
        'received_quantity',
        'hsn',
        'gst_rate',
        'item_subtotal',
        'taxable_amount',
        'gst_type',
        'cgst',
        'sgst',
        'igst',
    ];

    public function inventoryItem()
    {
        return $this->belongsTo(InventoryItem::class);
    }
    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

}
