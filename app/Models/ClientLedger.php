<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// app/Models/ClientLedger.php
class ClientLedger extends Model
{
    protected $fillable = [
        'client_id','date','type',
        'reference_type','reference_id',
        'debit','credit','balance'
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
