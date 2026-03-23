<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InventoryItem extends Model
{
    //
    use SoftDeletes;


    protected $fillable = [
        'unit_id',
        'brand_id',
        'vendor_id',
        'name',
        'sku',
        'description',
        'stocks',
        'minimum_stock',
        'purchase_price',
        'selling_price',
        'status',
        'gst_rate',
        'discount_type',
        'discount_value'
    ];


    // public function category()
    // {
    //     return $this->belongsTo(InventoryCategory::class, 'category_id');
    // }

    // public function categories()
    // {
    //     return $this->belongsToMany(
    //         InventoryCategory::class,
    //         'inventory_item_category'
    //     );
    // }


    // public function subCategory()
    // {
    //     return $this->belongsTo(InventoryCategory::class, 'sub_category_id');
    // }

    protected $casts = [
        'current_stock' => 'integer',
    ];

    // public function categories()
    // {
    //     return $this->belongsToMany(
    //         InventoryCategory::class,
    //         'inventory_item_category'
    //     )->wherePivot('type', 'category');
    // }

    // public function subCategories()
    // {
    //     return $this->belongsToMany(
    //         InventoryCategory::class,
    //         'inventory_item_category'
    //     )->wherePivot('type', 'sub_category');
    // }

    public function categories()
    {
        return $this->belongsToMany(
            InventoryCategory::class,
            'inventory_item_category',
            'inventory_item_id',
            'inventory_category_id'
        )->withPivot('type');
    }

    public function parentCategories()
    {
        return $this->categories()->wherePivot('type', 'category');
    }

    public function subCategories()
    {
        return $this->categories()->wherePivot('type', 'sub_category');
    }



    public function unit()
    {
        return $this->belongsTo(InventoryUnit::class);
    }
    public function brand()
    {
        return $this->belongsTo(InventoryBrand::class);
    }

    public function stockTransactions()
    {
        return $this->hasMany(StockTransaction::class);
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }
}
