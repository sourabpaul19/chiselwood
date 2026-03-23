<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\Client;
use App\Models\InventoryItem;
use Illuminate\Support\Facades\DB;
use App\Models\InvoiceItem;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\StockTransaction;
use App\Models\ClientLedger;
use App\Services\LedgerService;
use App\Services\FifoService;

class InvoiceController extends Controller
{
    protected $fifo;

    public function __construct(FifoService $fifo)
    {
        $this->fifo = $fifo;
    }
    /* ======================
       LIST
    ====================== */
    public function index()
    {
        $invoices = Invoice::with('client')
            ->latest()
            ->paginate(20);

        return view('admin.invoices.index', compact('invoices'));
    }

    /* ======================
       CREATE
    ====================== */
    public function create()
    {
        return view('admin.invoices.create', [
            'clients' => Client::where('status','active')->get(),
            'items'   => InventoryItem::where('status','active')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'client_id'              => 'required|exists:clients,id',
            'items'                  => 'required|array|min:1',
            'items.*.id'             => 'required|exists:inventory_items,id',
            'items.*.qty'            => 'required|numeric|min:1',
            'items.*.price'          => 'required|numeric|min:0', // GST INCLUDED
            'items.*.discount_type'  => 'nullable|in:percent,flat',
            'items.*.discount_value' => 'nullable|numeric|min:0',
            'discount'               => 'nullable|numeric|min:0', // invoice discount
        ]);

        DB::transaction(function () use ($request) {

            $client       = Client::findOrFail($request->client_id);
            //$companyState = strtolower(config('app.company_state', 'WB'));
            $companyState = setting('company_state');
            $clientState  = strtolower($client->client_state);

            $gstType = ($clientState === $companyState) ? 'cgst_sgst' : 'igst';

            $draftNo = 'DRAFT-' . now()->format('YmdHis'); // Temporary number

            /* ===============================
            CREATE INVOICE (EMPTY TOTALS)
            =============================== */
            $invoice = Invoice::create([
                //'invoice_no'      => 'INV-' . now()->format('YmdHis'),
                'invoice_no'     => $draftNo,
                'client_id'       => $client->id,
                'invoice_date'    => now(),
                'discount'        => $request->discount ?? 0,
                'gst_type'        => $gstType,
                'taxable_amount'  => 0,
                'subtotal'        => 0,
                'cgst'            => 0,
                'sgst'            => 0,
                'igst'            => 0,
                'grand_total'     => 0,
                'status'         => 'active',    // always 'active' for draft
                'is_final'       => 0,           // 0 = draft
            ]);

            $subtotal  = 0; // taxable total
            $totalCgst = 0;
            $totalSgst = 0;
            $totalIgst = 0;

            /* ===============================
            LOOP ITEMS
            =============================== */
            foreach ($request->items as $row) {

                $item    = InventoryItem::lockForUpdate()->findOrFail($row['id']); // 🔐 LOCK ROW
                $qty     = $row['qty'];
                $price   = $row['price']; // GST INCLUSIVE PRICE
                $gstRate = $item->gst_rate ?? 0;

                /* ===============================
                STOCK CHECK (NEW)
                =============================== */
                if ($item->stocks < $qty) {
                    throw new \Exception(
                        "Insufficient stock for item: {$item->name}"
                    );
                }

                $gross = $qty * $price;

                /* -------- ITEM DISCOUNT -------- */
                $discountType  = $row['discount_type']  ?? $item->discount_type ?? 'flat';
                $discountValue = $row['discount_value'] ?? $item->discount_value ?? 0;

                $discountAmount = ($discountType === 'percent')
                    ? ($gross * $discountValue / 100)
                    : $discountValue;

                $discountAmount = min($discountAmount, $gross);
                $grossAfterDiscount = $gross - $discountAmount;

                /* -------- GST INCLUSIVE EXTRACTION -------- */
                $taxableAmount = ($gstRate > 0)
                    ? ($grossAfterDiscount * 100) / (100 + $gstRate)
                    : $grossAfterDiscount;

                $gstAmount = $grossAfterDiscount - $taxableAmount;

                $cgst = $sgst = $igst = 0;

                if ($gstType === 'cgst_sgst') {
                    $cgst = $gstAmount / 2;
                    $sgst = $gstAmount / 2;
                } else {
                    $igst = $gstAmount;
                }

                $rowTotal = $grossAfterDiscount;

                /* ===============================
                SAVE INVOICE ITEM
                =============================== */
                InvoiceItem::create([
                    'invoice_id'        => $invoice->id,
                    'inventory_item_id' => $item->id,
                    'hsn'               => $item->sku,
                    'quantity'          => $qty,
                    'unit_price'        => $price,
                    'discount_type'     => $discountType,
                    'discount_value'    => $discountValue,
                    'discount_amount'   => $discountAmount,
                    'taxable_amount'    => $taxableAmount,
                    'gst_rate'          => $gstRate,
                    'gst_type'          => $gstType,
                    'cgst'              => $cgst,
                    'sgst'              => $sgst,
                    'igst'              => $igst,
                    'total_price'       => $rowTotal,
                ]);

                /* ===============================
                STOCK OUT (UPDATED)
                =============================== */
                // $item->stocks -= $qty;
                // $item->save();

                /* ===============================
                STOCK TRANSACTION LOG (NEW)
                =============================== */
                // StockTransaction::create([
                //     'inventory_item_id' => $item->id,
                //     'type'              => 'OUT',
                //     'quantity'          => $qty,
                //     'reference_type'    => 'invoice',
                //     'reference_id'      => $invoice->id,
                //     'note'              => 'Invoice #' . $invoice->invoice_no,
                // ]);

                $subtotal  += $taxableAmount;
                $totalCgst += $cgst;
                $totalSgst += $sgst;
                $totalIgst += $igst;
            }

            /* ===============================
            INVOICE DISCOUNT & TOTAL
            =============================== */
            $invoiceDiscount = $request->discount ?? 0;

            $grandTotal = max(
                ($subtotal + $totalCgst + $totalSgst + $totalIgst) - $invoiceDiscount,
                0
            );

            /* =====================
            3️⃣ 🔥 ADD LEDGER ENTRY (THIS PART)
            ===================== */
            ClientLedger::create([
                'client_id'      => $invoice->client_id,
                'date'           => $invoice->invoice_date,
                'type'           => 'invoice',
                'reference_type' => 'invoice',
                'reference_id'   => $invoice->id,
                'debit'          => $invoice->grand_total,
            ]);

            /* =====================
            4️⃣ RECALCULATE LEDGER BALANCE
            ===================== */
            LedgerService::recalculate($invoice->client_id);

            /* ===============================
            UPDATE INVOICE TOTALS
            =============================== */
            $invoice->update([
                'taxable_amount' => round($subtotal, 2),
                'subtotal'       => round($subtotal, 2),
                'cgst'           => round($totalCgst, 2),
                'sgst'           => round($totalSgst, 2),
                'igst'           => round($totalIgst, 2),
                'grand_total'    => round($grandTotal, 2),
            ]);
        });

        return redirect()
            ->route('admin.invoices.index')
            ->with('success', 'Invoice created successfully.');
    }



    /* ======================
       VIEW
    ====================== */
    public function show(Invoice $invoice)
    {
        $invoice->load('client','items.item','payments');
        return view('admin.invoices.show', compact('invoice'));
    }

    public function edit(Invoice $invoice)
    {
        if ($invoice->status === 'cancelled') {
            //abort(403, 'Cancelled invoice cannot be edited');
            return back()->with('error', 'Cancelled invoice cannot be edited');
        }

        if ($invoice->is_final) {
            //abort(403, 'Final invoice cannot be edited');
            return back()->with('error', 'Final invoice cannot be edited');
        }
       

        // 🔒 BLOCK EDIT IF PAYMENT EXISTS
        if ($invoice->payments()->exists()) {
            return redirect()
                ->route('admin.invoices.show', $invoice)
                ->with('error', 'This invoice has payments and cannot be edited.');
        }

        // 🔒 BLOCK IF ANY ACTIVE CREDIT NOTE EXISTS
        if ($invoice->creditNotes()->where('status', 'active')->exists()) {
            return redirect()
                ->route('admin.invoices.show', $invoice)
                ->with('error', 'This invoice has credit notes and cannot be edited.');
        }

        $invoice->load('items.item');

        return view('admin.invoices.edit', [
            'invoice' => $invoice,
            'clients' => Client::where('status','active')->get(),
            'items'   => InventoryItem::where('status','active')->get(),
        ]);
    }
    public function update(Request $request, Invoice $invoice)
    {
        $request->validate([
            'client_id' => 'required|exists:clients,id',
            'items'     => 'required|array|min:1',
            'items.*.id' => 'required|exists:inventory_items,id',
            'items.*.qty' => 'required|numeric|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.discount_type' => 'nullable|in:percent,flat',
            'items.*.discount_value' => 'nullable|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
        ]);

        DB::transaction(function () use ($request, $invoice) {

            /* ===============================
            1️⃣ RESTORE OLD STOCK (WITH LOG)
            =============================== */
            foreach ($invoice->items as $oldRow) {

                $item = InventoryItem::lockForUpdate()
                    ->findOrFail($oldRow->inventory_item_id);

                $item->stocks += $oldRow->quantity;
                $item->save();

                StockTransaction::create([
                    'inventory_item_id' => $item->id,
                    'type'              => 'IN',
                    'quantity'          => $oldRow->quantity,
                    'source'            => 'invoice_update_reverse',
                    'source_id'         => $invoice->id,
                    'note'              => 'Invoice edit rollback #' . $invoice->invoice_no,
                ]);
            }

            /* ===============================
            2️⃣ DELETE OLD ITEMS
            =============================== */
            $invoice->items()->delete();

            $subtotal = $totalCgst = $totalSgst = $totalIgst = 0;

            $client       = Client::findOrFail($request->client_id);
            //$companyState = strtolower(config('app.company_state', 'WB'));

            $companyState = setting('company_state');
            $clientState  = strtolower($client->client_state);
            $gstType      = ($clientState === $companyState) ? 'cgst_sgst' : 'igst';

            /* ===============================
            3️⃣ APPLY NEW ITEMS
            =============================== */
            foreach ($request->items as $row) {

                $item = InventoryItem::lockForUpdate()->findOrFail($row['id']);

                // STOCK CHECK
                if ($item->stocks < $row['qty']) {
                    throw new \Exception("Insufficient stock for {$item->name}");
                }

                $item->stocks -= $row['qty'];
                $item->save();

                StockTransaction::create([
                    'inventory_item_id' => $item->id,
                    'type'              => 'OUT',
                    'quantity'          => $row['qty'],
                    'reference_type'    => 'invoice_update',
                    'reference_id'      => $invoice->id,
                    'note'              => 'Invoice updated #' . $invoice->invoice_no,
                ]);

                /* ===== GST LOGIC (UNCHANGED) ===== */

                $qty   = $row['qty'];
                $price = $row['price'];
                $gstRate = $item->gst_rate ?? 0;

                $gross = $qty * $price;

                $discountType  = $row['discount_type'] ?? $item->discount_type ?? 'flat';
                $discountValue = $row['discount_value'] ?? $item->discount_value ?? 0;

                $discountAmount = ($discountType === 'percent')
                    ? ($gross * $discountValue / 100)
                    : $discountValue;

                $discountAmount = min($discountAmount, $gross);
                $grossAfterDiscount = $gross - $discountAmount;

                $taxableAmount = $gstRate > 0
                    ? ($grossAfterDiscount * 100) / (100 + $gstRate)
                    : $grossAfterDiscount;

                $gstAmount = $grossAfterDiscount - $taxableAmount;

                $cgst = $sgst = $igst = 0;

                if ($gstType === 'cgst_sgst') {
                    $cgst = $gstAmount / 2;
                    $sgst = $gstAmount / 2;
                } else {
                    $igst = $gstAmount;
                }

                $rowTotal = $grossAfterDiscount;

                $invoice->items()->create([
                    'inventory_item_id' => $item->id,
                    'quantity'          => $qty,
                    'hsn'               => $item->sku,
                    'unit_price'        => $price,
                    'discount_type'     => $discountType,
                    'discount_value'    => $discountValue,
                    'discount_amount'   => $discountAmount,
                    'taxable_amount'    => $taxableAmount,
                    'gst_rate'          => $gstRate,
                    'gst_type'          => $gstType,
                    'cgst'              => $cgst,
                    'sgst'              => $sgst,
                    'igst'              => $igst,
                    'total_price'       => $rowTotal,
                ]);

                $subtotal  += $taxableAmount;
                $totalCgst += $cgst;
                $totalSgst += $sgst;
                $totalIgst += $igst;
            }

            /* ===============================
            4️⃣ UPDATE INVOICE TOTALS
            =============================== */
            $invoiceDiscount = $request->discount ?? 0;

            $grandTotal = max(
                ($subtotal + $totalCgst + $totalSgst + $totalIgst) - $invoiceDiscount,
                0
            );

            $invoice->update([
                'client_id'      => $request->client_id,
                'taxable_amount' => round($subtotal, 2),
                'subtotal'       => round($subtotal, 2),
                'cgst'           => round($totalCgst, 2),
                'sgst'           => round($totalSgst, 2),
                'igst'           => round($totalIgst, 2),
                'discount'       => $invoiceDiscount,
                'gst_type'       => $gstType,
                'grand_total'    => round($grandTotal, 2),
            ]);
        });

        return redirect()
            ->route('admin.invoices.show', $invoice->id)
            ->with('success', 'Invoice updated successfully.');
    }

    public function cancel(Invoice $invoice)
    {
        // 🔒 SAFETY CHECK
        if ($invoice->payment_status !== 'unpaid') {
            return back()->with('error', 'Paid or partial invoices cannot be cancelled');
        }

        if ($invoice->status === 'cancelled') {
            return back()->with('error', 'Invoice already cancelled');
        }

        DB::transaction(function () use ($invoice) {

            /** -------------------------
             * RESTORE STOCK
             * ------------------------- */
            foreach ($invoice->items as $row) {
                $item = InventoryItem::lockForUpdate()->find($row->inventory_item_id);

                $item->increment('stocks', $row->quantity);
            }

            /** -------------------------
             * MARK INVOICE CANCELLED
             * ------------------------- */
            $invoice->update([
                'status'        => 'cancelled',
                'cancelled_at'  => now(),
            ]);
        });

        return redirect()
            ->route('admin.invoices.index')
            ->with('success', 'Invoice cancelled and stock restored');
    }

    public function pdf(Invoice $invoice)
    {
        
        $invoice->load(['client', 'items.item', 'payments']);

        $pdf = Pdf::loadView(
            'admin.invoices.pdf',
            compact('invoice')
        )->setPaper('A4');

        return $pdf->download(
            'Invoice-'.$invoice->invoice_no.'.pdf'
        );
    }

    public function ledger(Invoice $invoice)
    {
        $invoice->load(['client', 'payments', 'creditNotes']);

        $entries = collect();

        // 1️⃣ Invoice Entry (DEBIT)
        $entries->push([
            'date'        => $invoice->invoice_date ?? $invoice->created_at,
            'particulars' => 'Invoice #' . $invoice->invoice_no,
            'debit'       => $invoice->grand_total,
            'credit'      => 0,
        ]);

        // 2️⃣ Credit Notes (CREDIT)
        foreach ($invoice->creditNotes()->where('status', 'active')->get() as $cn) {
            $entries->push([
                'date'        => $cn->credit_date,
                'particulars' => 'Credit Note #' . $cn->credit_note_no,
                'debit'       => 0,
                'credit'      => $cn->grand_total,
            ]);
        }

        // 3️⃣ Payments (CREDIT)
        foreach ($invoice->payments as $payment) {
            $entries->push([
                'date'        => $payment->payment_date,
                'particulars' => 'Payment (' . $payment->payment_method . ')',
                'debit'       => 0,
                'credit'      => $payment->amount,
            ]);
        }

        // Sort by date
        $entries = $entries->sortBy('date')->values();

        // Running Balance
        $balance = 0;
        $entries = $entries->map(function ($row) use (&$balance) {
            $balance += $row['debit'];
            $balance -= $row['credit'];
            $row['balance'] = $balance;
            return $row;
        });

        return view('admin.invoices.ledger', compact('invoice', 'entries'));
    }

    public function finalize(Invoice $invoice)
    {
        if ($invoice->is_final) {
            return back()->with('error', 'Invoice already finalized');
        }

        DB::transaction(function () use ($invoice) {

            // ✅ 1️⃣ Lock Invoice
            $invoice->update([
                'invoice_no'     => 'INV-' . now()->format('YmdHis'),
                'is_final'       => true,
                'status'         => 'active',
                'payment_status' => 'unpaid',
                'finalized_at'   => now(),
            ]);

            foreach ($invoice->items as $row) {

                $item = InventoryItem::lockForUpdate()
                    ->findOrFail($row->inventory_item_id);

                /* ✅ STOCK CHECK AGAIN (VERY IMPORTANT) */
                if ($item->stocks < $row->quantity) {
                    throw new \Exception("Insufficient stock for {$item->name}");
                }

                /* ✅ FIFO COST */
                $fifoResult = $this->fifo->consume(
                    $row->inventory_item_id,
                    $row->quantity
                );

                $fifoCost = $fifoResult['total_cost'];

                /* ✅ UPDATE STOCK */
                // $item->stocks -= $row->quantity;
                // $item->save();

                $item->decrement('stocks', $row->quantity);

                /* ✅ STOCK LOG */
                StockTransaction::create([
                    'inventory_item_id' => $item->id,
                    'type'              => 'OUT',
                    'quantity'          => $row->quantity,
                    'reference_type'    => 'invoice',
                    'reference_id'      => $invoice->id,
                    'note'              => 'Invoice #' . $invoice->invoice_no,
                ]);

                /* ✅ PROFIT */
                $row->update([
                    'fifo_cost' => $fifoCost,
                    'profit'    => $row->taxable_amount - $fifoCost
                ]);
            }

            /* ✅ LEDGER */
            ClientLedger::create([
                'client_id'      => $invoice->client_id,
                'date'           => now(),
                'type'           => 'invoice',
                'reference_type' => 'invoice',
                'reference_id'   => $invoice->id,
                'debit'          => $invoice->grand_total,
            ]);

            LedgerService::recalculate($invoice->client_id);
        });

        return back()->with('success', 'Invoice finalized successfully');
    }
}

