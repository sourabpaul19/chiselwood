<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeType extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'status',
    ];

    /* ======================
       RELATIONSHIPS
    ====================== */

    public function staff()
    {
        return $this->hasMany(Staff::class);
    }
}
