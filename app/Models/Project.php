<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Project extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'project_id',
        'name',
        'client_id',
        'project_type_id',
        'project_status_id',
        'start_date',
        'estimated_end_date',
        'actual_end_date',
        'estimated_budget',
        'actual_cost',
        'location',
        'progress',
        'design_file',
        'notes',
        'status',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($project) {

            if (!$project->project_id) {
                $lastProject = self::withTrashed()
                    ->orderBy('id', 'desc')
                    ->first();

                $number = 1;

                if ($lastProject && $lastProject->project_id) {
                    $number = (int) substr($lastProject->project_id, 3) + 1;
                }

                $project->project_id = 'PRJ' . str_pad($number, 5, '0', STR_PAD_LEFT);
            }
        });
    }

    // public function client()
    // {
    //     return $this->belongsTo(Client::class);
    // }

    public function type()
    {
        return $this->belongsTo(ProjectType::class, 'project_type_id');
    }

    public function projectStatus()
    {
        return $this->belongsTo(ProjectStatus::class);
    }

    public function staffs(): BelongsToMany
    {
        return $this->belongsToMany(
            Staff::class,
            'project_staff',
            'project_id',
            'staff_id'
        )->withTimestamps();
    }

    public function vendors()
    {
        return $this->belongsToMany(Vendor::class, 'project_vendor');
    }

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }


}