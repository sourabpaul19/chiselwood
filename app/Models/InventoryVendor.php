<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InventoryVendor extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'inventory_vendor_id',
        'user_id',
        'contact_person',
        'phone',
        'email',
        'address',
        'inventory_vendor_state',
        'inventory_vendor_category_id',
        'pincode',
        'gstin',
        'cin',
        'payment_terms',
        'rating',
        'notes',
        'document',
        'status'
    ];

    /**
     * ✅ Auto Generate Vendor ID (like VND-0001)
     */
    protected static function booted()
    {
        static::creating(function ($vendor) {
            $lastId = self::withTrashed()->max('id') + 1;
            $vendor->inventory_vendor_id = 'IVND-' . str_pad($lastId, 4, '0', STR_PAD_LEFT);
        });
    }

    /**
     * ✅ Relationships
     */

    // 🔗 User Relation
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // 🔗 Category Relation
    public function category()
    {
        return $this->belongsTo(InventoryVendorCategory::class, 'inventory_vendor_category_id');
    }

    // 🔗 Projects (if needed like vendor)
    public function projects()
    {
        return $this->belongsToMany(Project::class, 'project_inventory_vendor', 'inventory_vendor_id', 'project_id');
    }

    // 🔗 Items (Inventory Items)
    public function items()
    {
        return $this->hasMany(InventoryItem::class, 'inventory_vendor_id');
    }
}