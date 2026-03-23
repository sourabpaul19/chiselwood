<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InventoryBrand extends Model
{
    use SoftDeletes;

    protected $fillable = ['name','slug','status'];

    public function items()
    {
        return $this->hasMany(InventoryItem::class,'brand_id');
    }

}
