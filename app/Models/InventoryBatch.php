<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryBatch extends Model
{
    protected $fillable = [
        'inventory_item_id',
        'quantity',
        'remaining_quantity',
        'unit_cost',
        'selling_price',   // ✅ ADD THIS
        'reference_type',
        'reference_id'
    ];

    public function item()
    {
        return $this->belongsTo(InventoryItem::class, 'inventory_item_id');
    }
}
