<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\GoodsReceipt;
use App\Models\PurchaseOrderItem;

class GoodsReceiptItem extends Model
{
    //
    protected $fillable = [
        'goods_receipt_id',
        'purchase_order_item_id',
        'received_quantity',
        'inventory_item_id',
        'unit_cost',
        'selling_price',
        'total',
    ];
    public function receipt()
    {
        return $this->belongsTo(GoodsReceipt::class, 'goods_receipt_id');
    }

    public function poItem()
    {
        return $this->belongsTo(PurchaseOrderItem::class, 'purchase_order_item_id');
    }
    public function inventoryItem()
    {
        return $this->belongsTo(InventoryItem::class, 'inventory_item_id');
    }

}
