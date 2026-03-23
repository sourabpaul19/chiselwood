<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = [
        'invoice_no',
        'client_id',
        'invoice_date',
        'subtotal',
        'tax',
        'discount',
        'grand_total',
        'payment_status', // 🔥 ADD THESE
        'status',
        'cancelled_at',
        'taxable_amount',
        'cgst',
        'sgst',
        'igst',
        'gst_type',
        'is_final',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
    public function creditNotes()
    {
        return $this->hasMany(CreditNote::class);
    }

    public function recalcInvoiceStatus()
    {
        $creditTotal = $this->creditNotes()
            ->where('status', 'active')
            ->sum('grand_total');

        $paymentTotal = $this->payments()->sum('amount');

        $due = $this->grand_total - $creditTotal - $paymentTotal;

        if ($due <= 0) {
            $this->update([
                'payment_status' => 'paid',
                'due_amount'     => 0
            ]);
        } elseif ($paymentTotal > 0 || $creditTotal > 0) {
            $this->update([
                'payment_status' => 'partial',
                'due_amount'     => $due
            ]);
        } else {
            $this->update([
                'payment_status' => 'unpaid',
                'due_amount'     => $this->grand_total
            ]);
        }
    }

}
