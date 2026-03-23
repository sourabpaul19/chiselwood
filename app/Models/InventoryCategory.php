<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InventoryCategory extends Model
{
    use SoftDeletes;

    protected $fillable = ['name','slug','parent_id','status'];

    // public function children()
    // {
    //     return $this->hasMany(self::class, 'parent_id')
    //         ->with('children');
    // }

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }
    // public function items()
    // {
    //     return $this->hasMany(InventoryItem::class, 'category_id');
    // }

    public function children()
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('name');
    }

    public function childrenRecursive()
    {
        return $this->hasMany(self::class, 'parent_id')
            ->where('status', 'active') // ✅ only active children
            ->with('childrenRecursive');
    }

    public function items()
    {
        return $this->belongsToMany(
            InventoryItem::class,
            'inventory_item_category'
        );
    }
}
