<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model {
    use SoftDeletes;

    protected $fillable = [
        'title',
        'project_id',
        'assigned_to',
        'priority_id',
        'status_id',
        'start_date',
        'due_date',
        'actual_due_date',
        'description',
        'documents',
        'status'
    ];

    protected static function booted()
    {
        static::creating(function ($task) {
            $last = Task::orderBy('id', 'desc')->first();
            $nextNumber = $last ? ((int) substr($last->task_id, 4)) + 1 : 1;

            $task->task_id = 'TASK' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
        });
    }


    protected $casts = [
        'documents' => 'array',
        'start_date' => 'date',
        'due_date' => 'date',
        'actual_due_date' => 'date',
    ];

    public function project() {
        return $this->belongsTo(Project::class);
    }

    public function assignedStaff() {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function priority() {
        return $this->belongsTo(TaskPriority::class);
    }

    public function statusInfo() {
        return $this->belongsTo(TaskStatus::class, 'status_id');
    }

    public function staffs()
    {
        return $this->belongsToMany(Staff::class, 'staff_task', 'task_id', 'staff_id');
    }

    public function assignees()
    {
        return $this->belongsToMany(User::class, 'task_user')->withTimestamps();;
    }
}
