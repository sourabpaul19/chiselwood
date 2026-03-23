<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InventoryVendorCategory extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'status'
    ];

    /**
     * 🔗 Relationship with Inventory Vendors
     */
    public function vendors()
    {
        return $this->hasMany(InventoryVendor::class, 'inventory_vendor_category_id');
    }
}