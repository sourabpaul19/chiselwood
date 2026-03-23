<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Staff extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'staff_id',
        'phone',
        'department_id',
        'employee_type_id',
        'designation',
        'skills',
        'salary',
        'document',
        'notes',
        'status',
    ];

    /* ======================
       RELATIONSHIPS
    ====================== */

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function employeeType()
    {
        return $this->belongsTo(EmployeeType::class);
    }

    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(
            Project::class,
            'project_staff',
            'staff_id',
            'project_id'
        )->withTimestamps();
    }

    /* ======================
       MODEL EVENTS
    ====================== */

    protected static function booted()
    {
        /* ======================
           AUTO STAFF ID (LIKE CLIENT)
           STF-0001, STF-0002
        ====================== */
        static::creating(function ($staff) {

            $lastNumber = DB::table('staff')
                ->select(DB::raw("MAX(CAST(SUBSTRING(staff_id, 5) AS UNSIGNED)) as max_no"))
                ->value('max_no');

            $nextNumber = $lastNumber ? $lastNumber + 1 : 1;

            $staff->staff_id = 'STF-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
        });

        /* ======================
           SOFT DELETE SYNC
        ====================== */
        static::deleting(function ($staff) {

            // Soft delete USER when STAFF soft deleted
            if (!$staff->isForceDeleting()) {
                $staff->user()->delete();
            }

            // Force delete USER when STAFF force deleted
            if ($staff->isForceDeleting()) {
                $staff->user()->withTrashed()->forceDelete();
            }
        });

        /* ======================
           RESTORE SYNC
        ====================== */
        static::restoring(function ($staff) {
            $staff->user()->withTrashed()->restore();
        });
    }
}
