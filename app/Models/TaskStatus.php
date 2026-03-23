<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TaskStatus extends Model {
    use SoftDeletes;

    protected $fillable = ['name', 'status'];

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function tasks() {
        return $this->hasMany(Task::class, 'status_id');
    }
}
