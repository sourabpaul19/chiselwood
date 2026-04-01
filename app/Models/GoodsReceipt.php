<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\PurchaseOrder;
use App\Models\InventoryVendor;

class GoodsReceipt extends Model
{
    //
    protected $fillable = [
        'receipt_number',
        'purchase_order_id',
        'receipt_date',
        'vendor_id',
        'created_by'
    ];

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function vendor()
    {
        return $this->belongsTo(InventoryVendor::class);
    }

    public function items()
    {
        return $this->hasMany(GoodsReceiptItem::class);
    }


}
