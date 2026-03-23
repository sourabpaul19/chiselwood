<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'client_id',
        'name',
        'email',
        'company_name',
        'phone',
        'address',
        'client_state', // ✅ ADD
        'social_media',
        'preferred_communication',
        'budget_range',
        'notes',
        'document',
        'status',
        'gstin',
        'cin',
        'pincode',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    

    // Auto-generate client ID
    protected static function booted()
    {
        static::creating(function ($client) {

            if (!$client->client_id) {

                $last = self::withTrashed()
                    ->orderBy('id', 'desc')
                    ->first();

                $number = 1;

                if ($last && $last->client_id) {
                    $number = (int) substr($last->client_id, 5) + 1;
                }

                $client->client_id = 'CLINT' . str_pad($number, 5, '0', STR_PAD_LEFT);
            }
        });
    }

    public function projects()
    {
        return $this->hasMany(Project::class, 'client_id');
    }


}
