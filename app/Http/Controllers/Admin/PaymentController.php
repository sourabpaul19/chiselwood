<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\ClientLedger;
use App\Services\LedgerService;
use App\Services\InvoiceService;


class PaymentController extends Controller
{
    //
    public function store(Request $request, Invoice $invoice)
    {
        if ($invoice->status === 'cancelled') {
            return back()->with('error', 'Cannot add payment to cancelled invoice');
        }
        if (!$invoice->is_final) {
            return back()->with('error', 'Finalize invoice before adding payment');
        }

        $request->validate([
            'amount'         => 'required|numeric|min:1',
            'payment_method' => 'required',
        ]);

        DB::transaction(function () use ($request, $invoice) {

            $payment = Payment::create([
                'invoice_id'     => $invoice->id,
                'amount'         => $request->amount,
                'payment_method' => $request->payment_method,
                'payment_date'   => now(),
                'receipt_no'     => 'RCPT-' . now()->format('YmdHis'),
            ]);

            ClientLedger::create([
                'client_id'      => $invoice->client_id,
                'date'           => now(),
                'type'           => 'payment',
                'reference_type' => 'payment',
                'reference_id'   => $payment->id,
                'credit'         => $payment->amount,
            ]);

            LedgerService::recalculate($invoice->client_id);

            InvoiceService::recalcInvoiceStatus($invoice);

            // 🔥 single source of truth
            //$invoice->recalcInvoiceStatus();
        });

        return back()->with('success', 'Payment recorded successfully');
    }

    public function downloadReceipt(Payment $payment)
    {
        $payment->load('invoice.client');

        $pdf = Pdf::loadView(
            'admin.payments.receipt-pdf',
            compact('payment')
        );

        return $pdf->download(
            'Payment-Receipt-' . $payment->receipt_no . '.pdf'
        );
    }



}
