<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InventoryUnit extends Model
{
    //
    use SoftDeletes;

    protected $fillable = ['name','short_name','status'];
}
