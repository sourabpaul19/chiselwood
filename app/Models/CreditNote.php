<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CreditNote extends Model
{
    //
    protected $fillable = [
        'credit_note_no','invoice_id','client_id','credit_date',
        'subtotal','taxable_amount','discount',
        'cgst','sgst','igst','gst_type',
        'grand_total','reason','status', 'original_credit_note_id', 'locked', 'reversal_created',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    // Link to client
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function items()
    {
        return $this->hasMany(CreditNoteItem::class);
    }

    protected $casts = [
        'credit_date' => 'datetime',
    ];
}
