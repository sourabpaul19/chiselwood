<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vendor extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'vendor_id','user_id','contact_person',
        'phone','email','address','vendor_category_id',
        'document','notes','status', 'pincode','vendor_state',
                'gstin'    ,
                'cin'      
    ];

    protected static function booted()
    {
        static::creating(function ($vendor) {
            $lastId = Vendor::withTrashed()->max('id') + 1;
            $vendor->vendor_id = 'VND-' . str_pad($lastId, 4, '0', STR_PAD_LEFT);
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(VendorCategory::class, 'vendor_category_id');
    }

    public function projects()
    {
        return $this->belongsToMany(Project::class, 'project_vendor', 'vendor_id', 'project_id');
    }
    public function items()
    {
        return $this->hasMany(InventoryItem::class);
    }

}
