<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model
{
    protected $fillable = [
        'invoice_id','inventory_item_id',
        'quantity','unit_price','total_price', 'discount_type', 'discount_value', 'discount_amount', 'item_subtotal', 'gst_rate', 'taxable_amount','cgst',
        'sgst',
        'igst',
        'gst_type','hsn', 'fifo_cost', 'profit',
    ];

    public function inventoryItem()
    {
        return $this->belongsTo(InventoryItem::class, 'inventory_item_id');
    }

    public function item()
    {
        return $this->belongsTo(InventoryItem::class, 'inventory_item_id');
    }
    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}
