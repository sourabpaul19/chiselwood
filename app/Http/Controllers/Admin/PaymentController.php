<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\ServiceInvoice;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\ClientLedger;
use App\Services\LedgerService;
use App\Services\InvoiceService;

class PaymentController extends Controller
{

    // ================= NORMAL INVOICE =================
    public function store(Request $request, Invoice $invoice)
    {
        if ($invoice->status === 'cancelled') {
            return back()->with('error', 'Cannot add payment to cancelled invoice');
        }

        if (!$invoice->is_final) {
            return back()->with('error', 'Finalize invoice before adding payment');
        }

        $request->validate([
            'amount' => 'required|numeric|min:1',
            'payment_method' => 'required',
        ]);



        DB::transaction(function () use ($request, $invoice) {

            // Calculate total paid BEFORE this payment
            $totalPaidBefore = $invoice->payments()->sum('amount');

            // Remaining due AFTER this payment
            $remainingDue = $invoice->grand_total - ($totalPaidBefore + $request->amount);

            $payment = Payment::create([
                'invoice_id'         => $invoice->id,
                'service_invoice_id' => null,
                'amount'             => $request->amount,
                'payment_method'     => $request->payment_method,
                'due_amount'         => max($remainingDue, 0), 
                'payment_date'       => now(),
                'receipt_no'         => 'RCPT-' . now()->format('YmdHis'),
            ]);

            ClientLedger::create([
                'client_id'      => $invoice->client_id,
                'date'           => now(),
                'type'           => 'payment',
                'reference_type' => 'invoice_payment',
                'reference_id'   => $payment->id,
                'credit'         => $payment->amount,
            ]);

            LedgerService::recalculate($invoice->client_id);
            InvoiceService::recalcInvoiceStatus($invoice);
        });

        return back()->with('success', 'Payment recorded successfully');
    }


    // ================= SERVICE INVOICE =================
    // ================= SERVICE INVOICE =================
    public function storeService(Request $request, ServiceInvoice $serviceInvoice)
    {
        if ($serviceInvoice->status === 'cancelled') {
            return back()->with('error', 'Cannot add payment to cancelled invoice');
        }

        if (!$serviceInvoice->is_final) {
            return back()->with('error', 'Finalize invoice before adding payment');
        }

        $request->validate([
            'amount' => 'required|numeric|min:1',
            'payment_method' => 'required',
        ]);

        DB::transaction(function () use ($request, $serviceInvoice) {

        // Calculate total paid BEFORE this payment
        $totalPaidBefore = $serviceInvoice->payments()->sum('amount');

        // Remaining due AFTER this payment
        $remainingDue = $serviceInvoice->grand_total - ($totalPaidBefore + $request->amount);

            $payment = Payment::create([
                'invoice_id'         => null,
                'service_invoice_id' => $serviceInvoice->id,
                'amount'             => $request->amount,
                'payment_method'     => $request->payment_method,
                'due_amount'         => max($remainingDue, 0), 
                'payment_date'       => now(),
                'receipt_no'         => 'SRV-RCPT-' . now()->format('YmdHis'),
            ]);

            ClientLedger::create([
                'client_id'      => $serviceInvoice->client_id,
                'date'           => now(),
                'type'           => 'payment',
                'reference_type' => 'service_payment',
                'reference_id'   => $payment->id,
                'credit'         => $payment->amount,
            ]);

            LedgerService::recalculate($serviceInvoice->client_id);

            // Call service invoice recalc method
            $serviceInvoice->recalcInvoiceStatus();
        });

        return back()->with('success', 'Service payment recorded successfully');
    }


    // ================= RECEIPT DOWNLOAD =================
    public function downloadReceipt(Payment $payment)
    {
        if ($payment->service_invoice_id) {

            // SERVICE RECEIPT
            $payment->load('serviceInvoice.client');

            $pdf = Pdf::loadView(
                'admin.service-payments.receipt-pdf',
                compact('payment')
            );

            return $pdf->download(
                'Service-Receipt-' . $payment->receipt_no . '.pdf'
            );

        } else {

            // NORMAL RECEIPT
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
}