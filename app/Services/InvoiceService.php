<?php 

namespace App\Services;

use App\Models\Invoice;
use App\Models\ServiceInvoice;

class InvoiceService
{
    public static function recalcInvoiceStatus($invoice)
    {
        // ================= SERVICE INVOICE =================
        if ($invoice instanceof ServiceInvoice) {

            $paymentTotal = $invoice->payments()->sum('amount');

            $due = $invoice->grand_total - $paymentTotal;

            if ($due <= 0) {
                $invoice->update([
                    'payment_status' => 'paid',
                    'due_amount'     => 0
                ]);
            } elseif ($paymentTotal > 0) {
                $invoice->update([
                    'payment_status' => 'partial',
                    'due_amount'     => $due
                ]);
            } else {
                $invoice->update([
                    'payment_status' => 'unpaid',
                    'due_amount'     => $invoice->grand_total
                ]);
            }

            return;
        }

        // ================= NORMAL INVOICE =================
        if ($invoice instanceof Invoice) {

            $creditTotal = $invoice->creditNotes()
                ->where('status','active')
                ->sum('grand_total');

            $paymentTotal = $invoice->payments()->sum('amount');

            $due = $invoice->grand_total - $creditTotal - $paymentTotal;

            if ($due <= 0) {
                $invoice->update([
                    'payment_status' => 'paid',
                    'due_amount'     => 0
                ]);
            } elseif ($paymentTotal > 0 || $creditTotal > 0) {
                $invoice->update([
                    'payment_status' => 'partial',
                    'due_amount'     => $due
                ]);
            } else {
                $invoice->update([
                    'payment_status' => 'unpaid',
                    'due_amount'     => $invoice->grand_total
                ]);
            }
        }
    }
}