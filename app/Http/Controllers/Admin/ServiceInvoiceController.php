<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ServiceInvoice;
use App\Models\ServiceInvoiceItem;
use App\Models\Client;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\ClientLedger;
use App\Services\LedgerService;

class ServiceInvoiceController extends Controller
{
    /* ======================
       LIST
    ====================== */
    public function index()
    {
        $invoices = ServiceInvoice::with('client')
            ->latest()
            ->paginate(20);

        return view('admin.service-invoices.index', compact('invoices'));
    }

    /* ======================
       CREATE
    ====================== */
    public function create()
    {
        return view('admin.service-invoices.create', [
            'clients' => Client::where('status','active')->get(),
        ]);
    }

    /* ======================
       STORE
    ====================== */
    public function store(Request $request)
    {
        $request->validate([
            'client_id' => 'required|exists:clients,id',
            'items'     => 'required|array|min:1',
            'items.*.name' => 'required',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.gst_rate' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
        ]);

        DB::transaction(function () use ($request) {

            $client = Client::findOrFail($request->client_id);

            $companyState = setting('company_state');
            $clientState  = strtolower($client->client_state);

            $gstType = ($clientState === strtolower($companyState)) ? 'cgst_sgst' : 'igst';

            $invoice = ServiceInvoice::create([
                'invoice_no'     => 'DRAFT-' . now()->format('YmdHis'),
                'client_id'      => $client->id,
                'invoice_date'   => now(),

                'gst_type'       => $gstType,

                'taxable_amount' => 0,
                'subtotal'       => 0,
                'cgst'           => 0,
                'sgst'           => 0,
                'igst'           => 0,
                'grand_total'    => 0,

                'discount'       => $request->discount ?? 0,
                'status'         => 'active',
                'is_final'       => 0,
            ]);

            $subtotal = $totalCgst = $totalSgst = $totalIgst = 0;

            foreach ($request->items as $row) {

                $price   = $row['price'];
                $gstRate = $row['gst_rate'];

                $taxable = ($gstRate > 0)
                    ? ($price * 100) / (100 + $gstRate)
                    : $price;

                $gstAmount = $price - $taxable;

                $cgst = $sgst = $igst = 0;

                if ($gstType === 'cgst_sgst') {
                    $cgst = $gstAmount / 2;
                    $sgst = $gstAmount / 2;
                } else {
                    $igst = $gstAmount;
                }

                ServiceInvoiceItem::create([
                    'service_invoice_id' => $invoice->id,
                    'service_name'       => $row['name'],
                    'unit_price'              => $price,
                    'taxable_amount'     => $taxable,
                    'gst_rate'           => $gstRate,
                    'cgst'               => $cgst,
                    'sgst'               => $sgst,
                    'igst'               => $igst,
                    'total_price'        => $price,
                ]);

                $subtotal  += $taxable;
                $totalCgst += $cgst;
                $totalSgst += $sgst;
                $totalIgst += $igst;
            }

            $discount = $request->discount ?? 0;

            $grandTotal = max(
                ($subtotal + $totalCgst + $totalSgst + $totalIgst) - $discount,
                0
            );

            $invoice->update([
                'taxable_amount' => round($subtotal, 2),
                'subtotal'       => round($subtotal, 2),
                'cgst'           => round($totalCgst, 2),
                'sgst'           => round($totalSgst, 2),
                'igst'           => round($totalIgst, 2),
                'grand_total'    => round($grandTotal, 2),
            ]);

            // Ledger Entry
            ClientLedger::create([
                'client_id' => $invoice->client_id,
                'date'      => $invoice->invoice_date,
                'type'      => 'service_invoice',
                'reference_type' => 'service_invoice',
                'reference_id' => $invoice->id,
                'debit'     => $invoice->grand_total,
            ]);

            LedgerService::recalculate($invoice->client_id);
        });

        return redirect()->route('admin.service-invoices.index')
            ->with('success', 'Service Invoice created');
    }

    /* ======================
       SHOW
    ====================== */
    public function show(ServiceInvoice $serviceInvoice)
    {
        $serviceInvoice->load('client','items','payments');
        return view('admin.service-invoices.show', compact('serviceInvoice'));
    }

    /* ======================
       EDIT
    ====================== */
    public function edit(ServiceInvoice $serviceInvoice)
    {
        if ($serviceInvoice->status === 'cancelled') {
            return back()->with('error','Cancelled invoice cannot be edited');
        }

        if ($serviceInvoice->is_final) {
            return back()->with('error','Final invoice cannot be edited');
        }

        if ($serviceInvoice->payments()->exists()) {
            return back()->with('error','Payment exists, cannot edit');
        }

        $serviceInvoice->load('items');

        return view('admin.service-invoices.edit', [
            'serviceInvoice' => $serviceInvoice,
            'clients' => Client::where('status','active')->get(),
        ]);
    }

    /* ======================
       UPDATE
    ====================== */
    public function update(Request $request, ServiceInvoice $serviceInvoice)
    {
        $request->validate([
            'client_id' => 'required|exists:clients,id',
            'items'     => 'required|array|min:1',
            'items.*.name' => 'required',
            'items.*.price' => 'required|numeric',
            'items.*.gst_rate' => 'required|numeric',
        ]);

        DB::transaction(function () use ($request, $serviceInvoice) {

            $serviceInvoice->items()->delete();

            $client = Client::findOrFail($request->client_id);

            $companyState = setting('company_state');
            $clientState  = strtolower($client->client_state);

            $gstType = ($clientState === strtolower($companyState)) ? 'cgst_sgst' : 'igst';

            $subtotal = $totalCgst = $totalSgst = $totalIgst = 0;

            foreach ($request->items as $row) {

                $price   = $row['price'];
                $gstRate = $row['gst_rate'];

                $taxable = ($gstRate > 0)
                    ? ($price * 100) / (100 + $gstRate)
                    : $price;

                $gstAmount = $price - $taxable;

                $cgst = $sgst = $igst = 0;

                if ($gstType === 'cgst_sgst') {
                    $cgst = $gstAmount / 2;
                    $sgst = $gstAmount / 2;
                } else {
                    $igst = $gstAmount;
                }

                $serviceInvoice->items()->create([
                    'service_name'   => $row['name'],
                    'unit_price'          => $price,
                    'taxable_amount' => $taxable,
                    'gst_rate'       => $gstRate,
                    'cgst'           => $cgst,
                    'sgst'           => $sgst,
                    'igst'           => $igst,
                    'total_price'    => $price,
                ]);

                $subtotal  += $taxable;
                $totalCgst += $cgst;
                $totalSgst += $sgst;
                $totalIgst += $igst;
            }

            $discount = $request->discount ?? 0;

            $grandTotal = max(
                ($subtotal + $totalCgst + $totalSgst + $totalIgst) - $discount,
                0
            );

            $serviceInvoice->update([
                'client_id'      => $request->client_id,
                'gst_type'       => $gstType,
                'taxable_amount' => $subtotal,
                'subtotal'       => $subtotal,
                'cgst'           => $totalCgst,
                'sgst'           => $totalSgst,
                'igst'           => $totalIgst,
                'discount'       => $discount,
                'grand_total'    => $grandTotal,
            ]);
        });

        return redirect()->route('admin.service-invoices.show', $serviceInvoice->id)
            ->with('success','Updated successfully');
    }

    /* ======================
       CANCEL
    ====================== */
    public function cancel(ServiceInvoice $serviceInvoice)
    {
        if ($serviceInvoice->payment_status !== 'unpaid') {
            return back()->with('error','Cannot cancel paid invoice');
        }

        $serviceInvoice->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
        ]);

        return back()->with('success','Cancelled successfully');
    }

    /* ======================
       PDF
    ====================== */
    public function pdf(ServiceInvoice $serviceInvoice)
    {
        $serviceInvoice->load('client','items');

        $pdf = Pdf::loadView(
            'admin.service-invoices.pdf',
            compact('serviceInvoice')
        );

        return $pdf->download('Service-Invoice-'.$serviceInvoice->invoice_no.'.pdf');
    }

    /* ======================
       LEDGER
    ====================== */
    public function ledger(ServiceInvoice $serviceInvoice)
    {
        $serviceInvoice->load('payments');

        $entries = collect();

        $entries->push([
            'date' => $serviceInvoice->invoice_date,
            'particulars' => 'Service Invoice #' . $serviceInvoice->invoice_no,
            'debit' => $serviceInvoice->grand_total,
            'credit' => 0,
        ]);

        foreach ($serviceInvoice->payments as $payment) {
            $entries->push([
                'date' => $payment->payment_date,
                'particulars' => 'Payment',
                'debit' => 0,
                'credit' => $payment->amount,
            ]);
        }

        return view('admin.service-invoices.ledger', compact('serviceInvoice','entries'));
    }

    /* ======================
       FINALIZE
    ====================== */
    public function finalize(ServiceInvoice $serviceInvoice)
    {
        if ($serviceInvoice->is_final) {
            return back()->with('error','Already finalized');
        }

        $serviceInvoice->update([
            'invoice_no'     => 'INV-' . now()->format('YmdHis'),
            'is_final'       => 1,
            'payment_status' => 'unpaid',
            'finalized_at'   => now(),
        ]);

        return back()->with('success','Finalized successfully');
    }
}