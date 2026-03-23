<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CreditNoteItem extends Model
{
    //
    protected $fillable = [
        'credit_note_id','invoice_item_id',
        'inventory_item_id','quantity',
        'unit_price','total_price', 'cgst','sgst','igst','gst_type','taxable_amount', 'hsn_code', 'fifo_cost', 'profit'
    ];

    public function creditNote()
    {
        return $this->belongsTo(CreditNote::class);
    }

    public function inventoryItem()
    {
        return $this->belongsTo(InventoryItem::class, 'inventory_item_id');
    }



}
