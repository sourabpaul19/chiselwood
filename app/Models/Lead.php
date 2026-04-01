<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lead extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'lead_id','name','email','phone','lead_source_id',
        'inquiry_date','budget_expectation','project_type_id',
        'lead_status_id','notes','follow_up_date','staff_id','status'
    ];

    public function leadSource() {
        return $this->belongsTo(LeadSource::class,'lead_source_id');
    }

    public function leadStatus() {
        return $this->belongsTo(LeadStatus::class,'lead_status_id');
    }

    // public function projectType() {
    //     return $this->belongsTo(ProjectType::class);
    // }

    public function type()
    {
        return $this->belongsTo(ProjectType::class, 'project_type_id');
    }

    public function staff()
    {
        return $this->belongsTo(Staff::class, 'staff_id');
    }

    protected static function booted()
    {
        static::creating(function ($lead) {

            if (!$lead->lead_id) {

                $last = self::withTrashed()
                    ->orderBy('id', 'desc')
                    ->first();

                $number = 1;

                if ($last && $last->lead_id) {
                    $number = (int) substr($last->lead_id, 4) + 1;
                }

                $lead->lead_id = 'LEAD' . str_pad($number, 5, '0', STR_PAD_LEFT);
            }
        });
    }
}
