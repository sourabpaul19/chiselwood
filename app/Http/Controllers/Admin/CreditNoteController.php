<?php

// namespace App\Http\Controllers\Admin;

// use App\Http\Controllers\Controller;
// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\DB;
// use App\Models\CreditNote;
// use App\Models\CreditNoteItem;
// use App\Models\Invoice;
// use App\Models\Client;
// use App\Models\InventoryItem;
// use App\Models\InventoryBatch;
// use App\Models\StockTransaction;
// use Barryvdh\DomPDF\Facade\Pdf;

// class CreditNoteController extends Controller
// {

//     /* ======================================================
//      CREATE FORM
//     ====================================================== */
//     public function create(Invoice $invoice)
//     {
//         if ($invoice->status === 'cancelled') {
//             abort(400, 'Cannot create credit note for cancelled invoice.');
//         }

//         if (!$invoice->is_final) {
//             abort(403, 'Draft invoice cannot have credit notes');
//         }

//         $invoice->load(['items.inventoryItem', 'client']);

//         return view('admin.credit-notes.create', [
//             'invoice' => $invoice,
//             'client' => $invoice->client,
//         ]);
//     }

//     /* ======================================================
//      STORE CREDIT NOTE (FIFO SAFE)
//     ====================================================== */
//     public function store(Request $request)
//     {
//         $request->validate([
//             'invoice_id' => 'required|exists:invoices,id',
//             'client_id' => 'required|exists:clients,id',
//             'items' => 'required|array',
//             'items.*.id' => 'required|exists:inventory_items,id',
//             'items.*.qty' => 'nullable|numeric|min:1',
//             'items.*.selected' => 'nullable|boolean',
//             'discount' => 'nullable|numeric|min:0',
//             'reason' => 'required|string|max:255',
//         ]);

//         DB::transaction(function () use ($request) {

//             /* ---------------- FILTER ITEMS ---------------- */
//             $items = collect($request->items)
//                 ->filter(fn ($i) => !empty($i['selected']))
//                 ->map(fn ($i) => [
//                     'id' => (int) $i['id'],
//                     'qty' => (int) ($i['qty'] ?? 0),
//                 ])
//                 ->filter(fn ($i) => $i['qty'] > 0)
//                 ->values();

//             if ($items->isEmpty()) {
//                 abort(400, 'Please select at least one item.');
//             }

//             $invoice = Invoice::with('items.inventoryItem')
//                 ->lockForUpdate()
//                 ->findOrFail($request->invoice_id);

//             $client = Client::findOrFail($request->client_id);

//             /* ---------------- VALIDATE OVER CREDIT ---------------- */
//             foreach ($items as $row) {

//                 $invoiceItem = $invoice->items
//                     ->firstWhere('inventory_item_id', $row['id']);

//                 if (!$invoiceItem) {
//                     abort(400, 'Item not found in invoice.');
//                 }

//                 $alreadyCredited = CreditNoteItem::join(
//                     'credit_notes',
//                     'credit_notes.id',
//                     '=',
//                     'credit_note_items.credit_note_id'
//                 )
//                     ->where('credit_notes.invoice_id', $invoice->id)
//                     ->where('credit_notes.status', 'active')
//                     ->where('credit_note_items.inventory_item_id', $row['id'])
//                     ->sum('credit_note_items.quantity');

//                 $remainingQty = $invoiceItem->quantity - $alreadyCredited;

//                 if ($row['qty'] > $remainingQty) {
//                     abort(400, 'Credit quantity exceeds remaining quantity.');
//                 }
//             }

//             /* ---------------- GST CALCULATION ---------------- */
//             $grossTotal = 0;
//             $taxableAmount = 0;
//             $cgst = $sgst = $igst = 0;

//             $companyState = strtolower(config('app.company_state', 'wb'));
//             $clientState = strtolower($client->client_state);
//             $gstType = ($companyState === $clientState) ? 'cgst_sgst' : 'igst';

//             foreach ($items as $row) {

//                 $invoiceItem = $invoice->items
//                     ->firstWhere('inventory_item_id', $row['id']);

//                 $ratio = $row['qty'] / $invoiceItem->quantity;

//                 $grossTotal += $row['qty'] * $invoiceItem->unit_price;
//                 $taxableAmount += $invoiceItem->taxable_amount * $ratio;

//                 if ($gstType === 'cgst_sgst') {
//                     $cgst += ($invoiceItem->cgst ?? 0) * $ratio;
//                     $sgst += ($invoiceItem->sgst ?? 0) * $ratio;
//                 } else {
//                     $igst += ($invoiceItem->igst ?? 0) * $ratio;
//                 }
//             }

//             $discount = min($request->discount ?? 0, $grossTotal);
//             $grandTotal = $grossTotal - $discount;

//             /* ---------------- CREATE CREDIT NOTE ---------------- */
//             $creditNote = CreditNote::create([
//                 'credit_note_no' => 'CN-' . now()->format('YmdHis'),
//                 'invoice_id' => $invoice->id,
//                 'client_id' => $client->id,
//                 'credit_date' => now(),
//                 'subtotal' => round($grossTotal, 2),
//                 'taxable_amount' => round($taxableAmount, 2),
//                 'discount' => round($discount, 2),
//                 'cgst' => round($cgst, 2),
//                 'sgst' => round($sgst, 2),
//                 'igst' => round($igst, 2),
//                 'gst_type' => $gstType,
//                 'grand_total' => round($grandTotal, 2),
//                 'reason' => $request->reason,
//                 'status' => 'active',
//             ]);

//             /* ---------------- PROCESS ITEMS (STOCK + BATCH) ---------------- */
//             foreach ($items as $row) {

//                 $invoiceItem = $invoice->items
//                     ->firstWhere('inventory_item_id', $row['id']);

//                 $ratio = $row['qty'] / $invoiceItem->quantity;

//                 /* Save credit note item */
//                 CreditNoteItem::create([
//                     'credit_note_id' => $creditNote->id,
//                     'inventory_item_id' => $row['id'],
//                     'quantity' => $row['qty'],
//                     'unit_price' => $invoiceItem->unit_price,
//                     'taxable_amount' => round($invoiceItem->taxable_amount * $ratio, 2),
//                     'cgst' => round($invoiceItem->cgst * $ratio, 2),
//                     'sgst' => round($invoiceItem->sgst * $ratio, 2),
//                     'igst' => round($invoiceItem->igst * $ratio, 2),
//                     'gst_rate' => $invoiceItem->gst_rate,
//                     'total_price' => round($invoiceItem->unit_price * $row['qty'], 2),
//                 ]);

//                 /* 1️⃣ Increase Stock */
//                 $item = InventoryItem::lockForUpdate()
//                     ->findOrFail($row['id']);

//                 $item->increment('stocks', $row['qty']);

//                 /* 2️⃣ Create FIFO Return Batch */
//                 $unitCost = $invoiceItem->fifo_cost / $invoiceItem->quantity;

//                 InventoryBatch::create([
//                     'inventory_item_id' => $item->id,
//                     'quantity' => $row['qty'],
//                     'remaining_quantity' => $row['qty'],
//                     'unit_cost' => $unitCost,
//                     'selling_price' => $invoiceItem->unit_price,
//                     'reference_type' => 'credit_note',
//                     'reference_id' => $creditNote->id,
//                 ]);

//                 /* 3️⃣ Stock Transaction */
//                 StockTransaction::create([
//                     'inventory_item_id' => $item->id,
//                     'type' => 'IN',
//                     'quantity' => $row['qty'],
//                     'reference_type' => 'credit_note',
//                     'reference_id' => $creditNote->id,
//                     'note' => 'Credit Note #' . $creditNote->credit_note_no,
//                 ]);
//             }

//             /* ---------------- UPDATE INVOICE STATUS ---------------- */
//             $paid = $invoice->payments()->sum('amount');
//             $credited = $invoice->creditNotes()
//                 ->where('status', 'active')
//                 ->sum('grand_total');

//             $due = max($invoice->grand_total - ($paid + $credited), 0);

//             $invoice->update([
//                 'payment_status' =>
//                     $due == 0 ? 'paid' :
//                     (($paid > 0 || $credited > 0) ? 'partial' : 'unpaid')
//             ]);
//         });

//         return redirect()
//             ->route('admin.invoices.show', $request->invoice_id)
//             ->with('success', 'Credit note created successfully');
//     }

//     /* ======================================================
//      DOWNLOAD PDF
//     ====================================================== */
//     public function downloadPdf(CreditNote $creditNote)
//     {
//         $pdf = Pdf::loadView('admin.credit-notes.pdf', compact('creditNote'));
//         return $pdf->download('CreditNote-' . $creditNote->credit_note_no . '.pdf');
//     }

//     public function cancel(CreditNote $creditNote)
//     {
//         if ($creditNote->status !== 'active') {
//             return back()->with('error', 'Only active credit notes can be cancelled.');
//         }

//         DB::transaction(function () use ($creditNote) {

//             $creditNote->load('items');

//             foreach ($creditNote->items as $row) {

//                 $item = InventoryItem::lockForUpdate()
//                     ->findOrFail($row->inventory_item_id);

//                 /* 1️⃣ Reduce stock */
//                 if ($item->stocks < $row->quantity) {
//                     throw new \Exception('Stock mismatch while cancelling credit note.');
//                 }

//                 $item->decrement('stocks', $row->quantity);

//                 /* 2️⃣ Consume FIFO (reverse return batch) */
//                 app(\App\Services\FifoService::class)
//                     ->consume($item->id, $row->quantity);

//                 /* 3️⃣ Stock transaction */
//                 StockTransaction::create([
//                     'inventory_item_id' => $item->id,
//                     'type' => 'OUT',
//                     'quantity' => $row->quantity,
//                     'reference_type' => 'credit_note_cancel',
//                     'reference_id' => $creditNote->id,
//                     'note' => 'Cancelled Credit Note #' . $creditNote->credit_note_no,
//                 ]);
//             }

//             /* 4️⃣ Mark cancelled */
//             $creditNote->update([
//                 'status' => 'cancelled',
//                 'locked' => true
//             ]);

//             /* 5️⃣ Update invoice payment status */
//             $invoice = $creditNote->invoice;

//             $paid = $invoice->payments()->sum('amount');
//             $credited = $invoice->creditNotes()
//                 ->where('status', 'active')
//                 ->sum('grand_total');

//             $due = max($invoice->grand_total - ($paid + $credited), 0);

//             $invoice->update([
//                 'payment_status' =>
//                     $due == 0 ? 'paid' :
//                     (($paid > 0 || $credited > 0) ? 'partial' : 'unpaid')
//             ]);
//         });

//         return back()->with('success', 'Credit note cancelled successfully.');
//     }

//     public function reversal(Request $request, CreditNote $creditNote)
//     {
//         $request->validate([
//             'reason' => 'required|string|max:255',
//         ]);

//         if ($creditNote->status !== 'cancelled') {
//             return back()->with('error', 'Only cancelled credit notes can be reversed.');
//         }

//         if ($creditNote->reversal_created) {
//             return back()->with('error', 'Reversal already created.');
//         }

//         DB::transaction(function () use ($creditNote, $request) {

//             $creditNote->load('items');

//             /* 1️⃣ Create reversal credit note */
//             $reversal = CreditNote::create([
//                 'credit_note_no' => 'CN-RV-' . now()->format('YmdHis'),
//                 'invoice_id' => $creditNote->invoice_id,
//                 'client_id' => $creditNote->client_id,
//                 'credit_date' => now(),
//                 'subtotal' => -$creditNote->subtotal,
//                 'taxable_amount' => -$creditNote->taxable_amount,
//                 'discount' => -$creditNote->discount,
//                 'cgst' => -$creditNote->cgst,
//                 'sgst' => -$creditNote->sgst,
//                 'igst' => -$creditNote->igst,
//                 'gst_type' => $creditNote->gst_type,
//                 'grand_total' => -$creditNote->grand_total,
//                 'reason' => $request->reason,
//                 'status' => 'reversal',
//                 'original_credit_note_id' => $creditNote->id,
//                 'locked' => true,
//             ]);

//             foreach ($creditNote->items as $row) {

//                 $item = InventoryItem::lockForUpdate()
//                     ->findOrFail($row->inventory_item_id);

//                 /* 2️⃣ Restore stock */
//                 $item->increment('stocks', $row->quantity);

//                 /* 3️⃣ Create new FIFO batch */
//                 $unitCost = $row->taxable_amount / $row->quantity;

//                 InventoryBatch::create([
//                     'inventory_item_id' => $item->id,
//                     'quantity' => $row->quantity,
//                     'remaining_quantity' => $row->quantity,
//                     'unit_cost' => $unitCost,
//                     'selling_price' => $row->unit_price,
//                     'reference_type' => 'credit_note_reversal',
//                     'reference_id' => $reversal->id,
//                 ]);

//                 /* 4️⃣ Stock transaction */
//                 StockTransaction::create([
//                     'inventory_item_id' => $item->id,
//                     'type' => 'IN',
//                     'quantity' => $row->quantity,
//                     'reference_type' => 'credit_note_reversal',
//                     'reference_id' => $reversal->id,
//                     'note' => 'Reversal of Credit Note #' . $creditNote->credit_note_no,
//                 ]);
//             }

//             /* 5️⃣ Mark original as reversed */
//             $creditNote->update([
//                 'reversal_created' => true
//             ]);

//             /* 6️⃣ Update invoice payment status */
//             $invoice = $creditNote->invoice;

//             $paid = $invoice->payments()->sum('amount');
//             $credited = $invoice->creditNotes()
//                 ->whereIn('status', ['active', 'reversal'])
//                 ->sum('grand_total');

//             $due = max($invoice->grand_total - ($paid + $credited), 0);

//             $invoice->update([
//                 'payment_status' =>
//                     $due == 0 ? 'paid' :
//                     (($paid > 0 || $credited != 0) ? 'partial' : 'unpaid')
//             ]);
//         });

//         return back()->with('success', 'Credit note reversed successfully.');
//     }
// }

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\CreditNote;
use App\Models\CreditNoteItem;
use App\Models\Invoice;
use App\Models\Client;
use App\Models\InventoryItem;
use App\Models\InventoryBatch;
use App\Models\StockTransaction;
use Barryvdh\DomPDF\Facade\Pdf;

class CreditNoteController extends Controller
{

    /* =========================================================
       CREATE FORM
    ========================================================== */
    public function create(Invoice $invoice)
    {
        if ($invoice->status === 'cancelled') {
            abort(400, 'Cannot create credit note for cancelled invoice.');
        }

        if (!$invoice->is_final) {
            abort(403, 'Draft invoice cannot have credit notes.');
        }

        $invoice->load(['items.inventoryItem', 'client']);

        return view('admin.credit-notes.create', compact('invoice'));
    }

    /* =========================================================
       STORE CREDIT NOTE (GST + FIFO + PROFIT SAFE)
    ========================================================== */
    public function store(Request $request)
    {
        $request->validate([
            'invoice_id' => 'required|exists:invoices,id',
            'client_id'  => 'required|exists:clients,id',
            'items'      => 'required|array',
            'reason'     => 'required|string|max:255',
        ]);

        DB::transaction(function () use ($request) {

            $invoice = Invoice::with('items')
                ->lockForUpdate()
                ->findOrFail($request->invoice_id);

            $client = Client::findOrFail($request->client_id);

            $companyState = strtolower(config('app.company_state', 'wb'));
            $clientState  = strtolower($client->client_state);

            $gstType = ($companyState === $clientState)
                ? 'cgst_sgst'
                : 'igst';

            $subtotal = 0;
            $taxableAmount = 0;
            $cgst = 0;
            $sgst = 0;
            $igst = 0;

            $creditNote = CreditNote::create([
                'credit_note_no' => 'CN-' . now()->format('YmdHis'),
                'invoice_id'     => $invoice->id,
                'client_id'      => $client->id,
                'credit_date'    => now(),
                'reason'         => $request->reason,
                'gst_type'       => $gstType,
                'status'         => 'active',
                'locked'         => true,
            ]);

            foreach ($request->items as $row) {

                if (empty($row['selected']) || $row['qty'] <= 0) {
                    continue;
                }

                $invoiceItem = $invoice->items
                    ->firstWhere('inventory_item_id', $row['id']);

                if (!$invoiceItem) {
                    abort(400, 'Item not found in invoice.');
                }

                $qty = (int) $row['qty'];

                if ($qty > $invoiceItem->quantity) {
                    abort(400, 'Return quantity exceeds sold quantity.');
                }

                $ratio = $qty / $invoiceItem->quantity;

                /* ================= GST CALCULATION ================= */

                $itemSubtotal = $invoiceItem->unit_price * $qty;
                $itemTaxable  = $invoiceItem->taxable_amount * $ratio;
                $itemCgst     = $invoiceItem->cgst * $ratio;
                $itemSgst     = $invoiceItem->sgst * $ratio;
                $itemIgst     = $invoiceItem->igst * $ratio;

                $subtotal      += $itemSubtotal;
                $taxableAmount += $itemTaxable;
                $cgst          += $itemCgst;
                $sgst          += $itemSgst;
                $igst          += $itemIgst;

                /* ================= PROFIT REVERSAL ================= */

                $fifoUnitCost = $invoiceItem->fifo_cost / $invoiceItem->quantity;
                $profit       = ($invoiceItem->unit_price - $fifoUnitCost) * $qty;

                CreditNoteItem::create([
                    'credit_note_id'    => $creditNote->id,
                    'inventory_item_id' => $invoiceItem->inventory_item_id,
                    'quantity'          => $qty,
                    'unit_price'        => $invoiceItem->unit_price,
                    'taxable_amount'    => round($itemTaxable, 2),
                    'cgst'              => round($itemCgst, 2),
                    'sgst'              => round($itemSgst, 2),
                    'igst'              => round($itemIgst, 2),
                    'gst_rate'          => $invoiceItem->gst_rate,
                    'fifo_cost'         => round($fifoUnitCost * $qty, 2),
                    'profit'            => round($profit, 2),
                    'total_price'       => round($itemSubtotal, 2),
                ]);

                /* ================= STOCK RESTORE ================= */

                $item = InventoryItem::lockForUpdate()
                    ->findOrFail($invoiceItem->inventory_item_id);

                $item->increment('stocks', $qty);

                InventoryBatch::create([
                    'inventory_item_id' => $item->id,
                    'qty_remaining'     => $qty,
                    'unit_cost'         => $fifoUnitCost,
                    'source_type'       => 'credit_note',
                    'source_id'         => $creditNote->id,
                ]);

                StockTransaction::create([
                    'inventory_item_id' => $item->id,
                    'type'              => 'IN',
                    'quantity'          => $qty,
                    'reference_type'    => 'credit_note',
                    'reference_id'      => $creditNote->id,
                    'note'              => 'Credit Note #' . $creditNote->credit_note_no,
                ]);
            }

            $creditNote->update([
                'subtotal'       => round($subtotal, 2),
                'taxable_amount' => round($taxableAmount, 2),
                'cgst'           => round($cgst, 2),
                'sgst'           => round($sgst, 2),
                'igst'           => round($igst, 2),
                'grand_total'    => round($subtotal, 2),
            ]);

            $this->updateInvoicePaymentStatus($invoice);
        });

        return back()->with('success', 'Credit note created successfully.');
    }


    /* =========================================================
       CANCEL CREDIT NOTE
    ========================================================== */
    public function cancel(CreditNote $creditNote)
    {
        if ($creditNote->status !== 'active') {
            return back()->with('error', 'Only active credit notes can be cancelled.');
        }

        DB::transaction(function () use ($creditNote) {

            $creditNote->load('items');

            foreach ($creditNote->items as $row) {

                $item = InventoryItem::lockForUpdate()
                    ->findOrFail($row->inventory_item_id);

                if ($item->stocks < $row->quantity) {
                    throw new \Exception('Stock mismatch while cancelling.');
                }

                $item->decrement('stocks', $row->quantity);

                app(\App\Services\FifoService::class)
                    ->consume($item->id, $row->quantity);

                StockTransaction::create([
                    'inventory_item_id' => $item->id,
                    'type'              => 'OUT',
                    'quantity'          => $row->quantity,
                    'reference_type'    => 'credit_note_cancel',
                    'reference_id'      => $creditNote->id,
                    'note'              => 'Cancel CN #' . $creditNote->credit_note_no,
                ]);
            }

            $creditNote->update([
                'status' => 'cancelled',
                'locked' => true,
            ]);

            $this->updateInvoicePaymentStatus($creditNote->invoice);
        });

        return back()->with('success', 'Credit note cancelled successfully.');
    }


    /* =========================================================
       REVERSAL CREDIT NOTE
    ========================================================== */
    public function reversal(Request $request, CreditNote $creditNote)
    {
        if ($creditNote->status !== 'cancelled') {
            return back()->with('error', 'Only cancelled credit notes can be reversed.');
        }

        if ($creditNote->reversal_created) {
            return back()->with('error', 'Reversal already created.');
        }

        DB::transaction(function () use ($creditNote) {

            $creditNote->load('items');

            $reversal = CreditNote::create([
                'credit_note_no' => 'CN-RV-' . now()->format('YmdHis'),
                'invoice_id' => $creditNote->invoice_id,
                'client_id'  => $creditNote->client_id,
                'credit_date'=> now(),
                'subtotal'   => -$creditNote->subtotal,
                'taxable_amount' => -$creditNote->taxable_amount,
                'cgst'       => -$creditNote->cgst,
                'sgst'       => -$creditNote->sgst,
                'igst'       => -$creditNote->igst,
                'grand_total'=> -$creditNote->grand_total,
                'gst_type'   => $creditNote->gst_type,
                'status'     => 'reversal',
                'original_credit_note_id' => $creditNote->id,
                'locked'     => true,
            ]);

            foreach ($creditNote->items as $row) {

                CreditNoteItem::create([
                    'credit_note_id'    => $reversal->id,
                    'inventory_item_id' => $row->inventory_item_id,
                    'quantity'          => $row->quantity,
                    'unit_price'        => $row->unit_price,
                    'fifo_cost'         => $row->fifo_cost,
                    'profit'            => -$row->profit,
                    'total_price'       => -$row->total_price,
                ]);
            }

            $creditNote->update([
                'reversal_created' => true
            ]);

            $this->updateInvoicePaymentStatus($creditNote->invoice);
        });

        return back()->with('success', 'Credit note reversal created successfully.');
    }


    /* =========================================================
       DOWNLOAD PDF
    ========================================================== */
    public function downloadPdf(CreditNote $creditNote)
    {
        $pdf = Pdf::loadView('admin.credit-notes.pdf', compact('creditNote'));
        return $pdf->download('CreditNote-' . $creditNote->credit_note_no . '.pdf');
    }


    /* =========================================================
       PAYMENT STATUS HELPER
    ========================================================== */
    private function updateInvoicePaymentStatus($invoice)
    {
        $paid = $invoice->payments()->sum('amount');

        $credited = $invoice->creditNotes()
            ->where('status', 'active')
            ->sum('grand_total');

        $due = max($invoice->grand_total - ($paid + $credited), 0);

        $invoice->update([
            'payment_status' =>
                $due == 0 ? 'paid' :
                (($paid > 0 || $credited > 0) ? 'partial' : 'unpaid')
        ]);
    }
}
