<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceInvoice extends Model
{
    protected $fillable = [
        'invoice_no','client_id','invoice_date',
        'subtotal','taxable_amount',
        'cgst','sgst','igst',
        'discount','grand_total',
        'gst_type','payment_status','status','is_final'
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function items()
    {
        return $this->hasMany(ServiceInvoiceItem::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
    // ================= SERVICE INVOICE PAYMENT STATUS =================
    public function recalcInvoiceStatus()
    {
        $paymentTotal = $this->payments()->sum('amount');
        $due = $this->grand_total - $paymentTotal;

        if ($due <= 0) {
            $this->update([
                'payment_status' => 'paid',
                'due_amount'     => 0,
            ]);
        } elseif ($paymentTotal > 0) {
            $this->update([
                'payment_status' => 'partial',
                'due_amount'     => $due,
            ]);
        } else {
            $this->update([
                'payment_status' => 'unpaid',
                'due_amount'     => $this->grand_total,
            ]);
        }
    }
}