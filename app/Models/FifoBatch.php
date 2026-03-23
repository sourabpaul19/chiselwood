<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FifoBatch extends Model
{
    protected $fillable = [
        'inventory_item_id',
        'qty_remaining',
        'unit_cost',
        'selling_price',   // ✅ ADD THIS
        'source_type',
        'source_id',
    ];


    public function item()
    {
        return $this->belongsTo(InventoryItem::class, 'inventory_item_id');
    }
}

