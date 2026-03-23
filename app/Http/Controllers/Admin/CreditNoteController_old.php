<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CreditNote;
use App\Models\CreditNoteItem;
use App\Models\Invoice;
use App\Models\Client;
use App\Models\InventoryItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\StockTransaction;


class CreditNoteController extends Controller
{
    /**
     * Show form to create a credit note for an invoice
     */
    public function create(Invoice $invoice)
    {
        if ($invoice->status === 'cancelled') {
            abort(400, 'Cannot create credit note for cancelled invoice.');
        }

        if (!$invoice->is_final) {
            abort(403, 'Draft invoice cannot have credit notes');
        }

        $invoice->load(['items.inventoryItem', 'client']);

        return view('admin.credit-notes.create', [
            'invoice' => $invoice,
            'client'  => $invoice->client,
        ]);
    }

    /**
     * Store a new credit note
     */
    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'invoice_id'       => 'required|exists:invoices,id',
    //         'client_id'        => 'required|exists:clients,id',
    //         'items'            => 'required|array',
    //         'items.*.id'       => 'required|exists:inventory_items,id',
    //         'items.*.qty'      => 'nullable|numeric|min:1',
    //         'items.*.selected' => 'nullable|boolean',
    //         'discount'         => 'nullable|numeric|min:0',
    //         'reason'           => 'required|string|max:255',
    //     ]);

    //     DB::transaction(function () use ($request) {

    //         /* ===============================
    //            FILTER ONLY SELECTED ITEMS
    //         =============================== */
    //         $items = collect($request->items)
    //             ->filter(fn($item) => !empty($item['selected']))
    //             ->map(fn($item) => [
    //                 'id'  => (int) $item['id'],
    //                 'qty' => (int) ($item['qty'] ?? 0),
    //             ])
    //             ->filter(fn($item) => $item['qty'] > 0)
    //             ->values();

    //         if ($items->isEmpty()) {
    //             abort(400, 'Please select at least one item with quantity.');
    //         }

    //         $invoice = Invoice::with('items')->findOrFail($request->invoice_id);

    //         if ($invoice->status === 'cancelled') {
    //             abort(400, 'Cannot create credit note for cancelled invoice.');
    //         }

    //         $client = Client::findOrFail($request->client_id);

    //         /* ===============================
    //            PREVENT OVER-CREDITING
    //         =============================== */
    //         foreach ($items as $row) {
    //             $invoiceItem = $invoice->items
    //                 ->firstWhere('inventory_item_id', $row['id']);

    //             if (!$invoiceItem) {
    //                 abort(400, 'Item not found in invoice');
    //             }

    //             $alreadyCredited = CreditNoteItem::join(
    //                     'credit_notes',
    //                     'credit_notes.id',
    //                     '=',
    //                     'credit_note_items.credit_note_id'
    //                 )
    //                 ->where('credit_notes.invoice_id', $invoice->id)
    //                 ->where('credit_note_items.inventory_item_id', $row['id'])
    //                 ->sum('credit_note_items.quantity');

    //             $remainingQty = $invoiceItem->quantity - $alreadyCredited;

    //             if ($row['qty'] > $remainingQty) {
    //                 abort(400, "Credit qty exceeds remaining invoice qty for item {$invoiceItem->inventoryItem->name}");
    //             }
    //         }

    //         /* ===============================
    //            CALCULATE SUBTOTAL & GST
    //         =============================== */
    //         $subtotal      = 0;
    //         $cgst = $sgst = $igst = 0;
    //         $companyState  = strtolower(config('app.company_state', 'WB'));
    //         $clientState   = strtolower($client->client_state);

    //         foreach ($items as $row) {
    //             $invoiceItem = $invoice->items
    //                 ->firstWhere('inventory_item_id', $row['id']);

    //             $subtotal += $row['qty'] * $invoiceItem->unit_price;

    //             $ratio = $row['qty'] / $invoiceItem->quantity;

    //             if ($companyState === $clientState) {
    //                 $cgst += ($invoiceItem->cgst ?? 0) * $ratio;
    //                 $sgst += ($invoiceItem->sgst ?? 0) * $ratio;
    //                 $gstType = 'cgst_sgst';
    //             } else {
    //                 $igst += ($invoiceItem->igst ?? 0) * $ratio;
    //                 $gstType = 'igst';
    //             }
    //         }

    //         $discount      = $request->discount ?? 0;
    //         $taxableAmount = max($subtotal - $discount, 0);
    //         $grandTotal    = $taxableAmount + $cgst + $sgst + $igst;

    //         /* ===============================
    //            CREATE CREDIT NOTE
    //         =============================== */
    //         $creditNote = CreditNote::create([
    //             'credit_note_no' => 'CN-' . now()->format('YmdHis'),
    //             'invoice_id'     => $invoice->id,
    //             'client_id'      => $client->id,
    //             'credit_date'    => now(),
    //             'subtotal'       => round($subtotal, 2),
    //             'taxable_amount' => round($taxableAmount, 2),
    //             'discount'       => round($discount, 2),
    //             'cgst'           => round($cgst, 2),
    //             'sgst'           => round($sgst, 2),
    //             'igst'           => round($igst, 2),
    //             'gst_type'       => $gstType,
    //             'grand_total'    => round($grandTotal, 2),
    //             'reason'         => $request->reason,
    //             'status'         => 'active',
    //         ]);

    //         /* ===============================
    //            CREATE CREDIT NOTE ITEMS & REVERSE STOCK
    //         =============================== */
    //         foreach ($items as $row) {
    //             $invoiceItem = $invoice->items
    //                 ->firstWhere('inventory_item_id', $row['id']);

    //             $ratio = $row['qty'] / $invoiceItem->quantity;

    //             CreditNoteItem::create([
    //                 'credit_note_id'    => $creditNote->id,
    //                 'inventory_item_id' => $row['id'],
    //                 'quantity'          => $row['qty'],
    //                 'unit_price'        => $invoiceItem->unit_price,
    //                 'taxable_amount'    => round($invoiceItem->taxable_amount * $ratio, 2),
    //                 'cgst'              => round($invoiceItem->cgst * $ratio, 2),
    //                 'sgst'              => round($invoiceItem->sgst * $ratio, 2),
    //                 'igst'              => round($invoiceItem->igst * $ratio, 2),
    //                 'gst_rate'          => $invoiceItem->gst_rate,
    //                 'hsn_code'          => $invoiceItem->hsn_code,
    //                 'total_price'       => round($row['qty'] * $invoiceItem->unit_price, 2),
    //             ]);

    //             // Add stock back
    //             InventoryItem::where('id', $row['id'])->increment('current_stock', $row['qty']);
    //         }
    //     });

    //     return redirect()
    //         ->route('admin.invoices.show', $request->invoice_id)
    //         ->with('success', 'Credit Note created successfully');
    // }

    public function store(Request $request)
    {
        $request->validate([
            'invoice_id'       => 'required|exists:invoices,id',
            'client_id'        => 'required|exists:clients,id',
            'items'            => 'required|array',
            'items.*.id'       => 'required|exists:inventory_items,id',
            'items.*.qty'      => 'nullable|numeric|min:1',
            'items.*.selected' => 'nullable|boolean',
            'discount'         => 'nullable|numeric|min:0',
            'reason'           => 'required|string|max:255',
        ]);

        DB::transaction(function () use ($request) {

            /* ===============================
               FILTER SELECTED ITEMS
            =============================== */
            $items = collect($request->items)
                ->filter(fn ($i) => !empty($i['selected']))
                ->map(fn ($i) => [
                    'id'  => (int) $i['id'],
                    'qty' => (int) ($i['qty'] ?? 0),
                ])
                ->filter(fn ($i) => $i['qty'] > 0)
                ->values();

            if ($items->isEmpty()) {
                abort(400, 'Please select at least one item with quantity.');
            }

            /* ===============================
               LOAD INVOICE
            =============================== */
            $invoice = Invoice::with('items.inventoryItem')
                ->findOrFail($request->invoice_id);

            if ($invoice->status === 'cancelled') {
                abort(400, 'Cannot create credit note for cancelled invoice.');
            }

            $client = Client::findOrFail($request->client_id);

            /* ===============================
               PREVENT OVER CREDITING
            =============================== */
            foreach ($items as $row) {
                $invoiceItem = $invoice->items
                    ->firstWhere('inventory_item_id', $row['id']);

                if (!$invoiceItem) {
                    abort(400, 'Item not found in invoice.');
                }

                $alreadyCredited = CreditNoteItem::join(
                        'credit_notes',
                        'credit_notes.id',
                        '=',
                        'credit_note_items.credit_note_id'
                    )
                    ->where('credit_notes.invoice_id', $invoice->id)
                    ->where('credit_notes.status', 'active')
                    ->where('credit_note_items.inventory_item_id', $row['id'])
                    ->sum('credit_note_items.quantity');

                $remainingQty = $invoiceItem->quantity - $alreadyCredited;

                if ($row['qty'] > $remainingQty) {
                    abort(400, "Credit qty exceeds remaining qty for {$invoiceItem->inventoryItem->name}");
                }
            }

            /* ===============================
               GST INCLUSIVE CALCULATION
            =============================== */
            $grossTotal = 0;
            $taxableAmount = 0;
            $cgst = $sgst = $igst = 0;

            $companyState = strtolower(config('app.company_state', 'wb'));
            $clientState  = strtolower($client->client_state);
            $gstType      = ($companyState === $clientState) ? 'cgst_sgst' : 'igst';

            foreach ($items as $row) {
                $invoiceItem = $invoice->items
                    ->firstWhere('inventory_item_id', $row['id']);

                $ratio = $row['qty'] / $invoiceItem->quantity;

                $gross = $row['qty'] * $invoiceItem->unit_price;
                $grossTotal += $gross;

                $taxableAmount += $invoiceItem->taxable_amount * $ratio;

                if ($gstType === 'cgst_sgst') {
                    $cgst += ($invoiceItem->cgst ?? 0) * $ratio;
                    $sgst += ($invoiceItem->sgst ?? 0) * $ratio;
                } else {
                    $igst += ($invoiceItem->igst ?? 0) * $ratio;
                }
            }

            /* ===============================
               DISCOUNT (INCLUSIVE)
            =============================== */
            $discount = min($request->discount ?? 0, $grossTotal);

            if ($discount > 0) {
                $ratio = ($grossTotal - $discount) / $grossTotal;

                $taxableAmount *= $ratio;
                $cgst *= $ratio;
                $sgst *= $ratio;
                $igst *= $ratio;
            }

            $grandTotal = $grossTotal - $discount;

            /* ===============================
               CREATE CREDIT NOTE
            =============================== */
            $creditNote = CreditNote::create([
                'credit_note_no' => 'CN-' . now()->format('YmdHis'),
                'invoice_id'     => $invoice->id,
                'client_id'      => $client->id,
                'credit_date'    => now(),
                'subtotal'       => round($grossTotal, 2),
                'taxable_amount' => round($taxableAmount, 2),
                'discount'       => round($discount, 2),
                'cgst'           => round($cgst, 2),
                'sgst'           => round($sgst, 2),
                'igst'           => round($igst, 2),
                'gst_type'       => $gstType,
                'grand_total'    => round($grandTotal, 2),
                'reason'         => $request->reason,
                'status'         => 'active',
            ]);

            /* ===============================
               CREDIT NOTE ITEMS + STOCK BACK
            =============================== */
            foreach ($items as $row) {
                $invoiceItem = $invoice->items
                    ->firstWhere('inventory_item_id', $row['id']);

                $ratio = $row['qty'] / $invoiceItem->quantity;

                CreditNoteItem::create([
                    'credit_note_id'    => $creditNote->id,
                    'inventory_item_id' => $row['id'],
                    'quantity'          => $row['qty'],
                    'unit_price'        => $invoiceItem->unit_price,
                    'taxable_amount'    => round($invoiceItem->taxable_amount * $ratio, 2),
                    'cgst'              => round($invoiceItem->cgst * $ratio, 2),
                    'sgst'              => round($invoiceItem->sgst * $ratio, 2),
                    'igst'              => round($invoiceItem->igst * $ratio, 2),
                    'gst_rate'          => $invoiceItem->gst_rate,
                    'hsn_code'          => $invoiceItem->hsn_code,
                    'total_price'       => round($invoiceItem->unit_price * $row['qty'], 2),
                ]);

                InventoryItem::where('id', $row['id'])
                    ->increment('current_stock', $row['qty']);
            }

            /* ===============================
               UPDATE INVOICE PAYMENT STATUS
            =============================== */
            $paid = $invoice->payments()->sum('amount');
            $credited = $invoice->creditNotes()
                ->where('status', 'active')
                ->sum('grand_total');

            $due = max($invoice->grand_total - ($paid + $credited), 0);

            $invoice->update([
                'payment_status' => $due == 0 ? 'paid' : ($paid > 0 || $credited > 0 ? 'partial' : 'unpaid')
            ]);
        });

        return redirect()
            ->route('admin.invoices.show', $request->invoice_id)
            ->with('success', 'Credit note created successfully');
    }

    /**
     * Show form to edit credit note
     */
    public function edit(CreditNote $creditNote)
    {
        if ($creditNote->status !== 'active') {
            abort(400, 'Cannot edit cancelled credit note.');
        }
        

        $creditNote->load(['invoice.items.inventoryItem', 'client']);
        return view('admin.credit-notes.edit', compact('creditNote'));
    }

    /**
     * Update credit note
     */
//     public function update(Request $request, CreditNote $creditNote)
// {
//     $request->validate([
//         'items'            => 'required|array',
//         'items.*.id'       => 'required|exists:inventory_items,id',
//         'items.*.qty'      => 'nullable|numeric|min:0',
//         'items.*.selected' => 'nullable|boolean',
//         'discount'         => 'nullable|numeric|min:0',
//         'reason'           => 'required|string|max:255',
//     ]);

//     DB::transaction(function () use ($request, $creditNote) {

//         // Only allow editing active credit notes
//         // if ($creditNote->status !== 'active') {
//         //     return redirect()->back()->withErrors('Cannot edit cancelled credit note.');
//         // }

//         if ($creditNote->status !== 'active') {
//             throw new \Exception('Cannot edit cancelled credit note.');
//         }


//         $items = collect($request->items)
//             ->filter(fn($item) => !empty($item['selected']))
//             ->map(fn($item) => [
//                 'id'  => (int) $item['id'],
//                 'qty' => (int) ($item['qty'] ?? 0),
//             ])
//             ->filter(fn($item) => $item['qty'] > 0)
//             ->values();

//         if ($items->isEmpty()) {
//             return redirect()->back()->withErrors('Please select at least one item with quantity.');
//         }

//         $invoice = $creditNote->invoice;
//         $client  = $creditNote->client;

//         // Reverse old stock
//         foreach ($creditNote->items as $item) {
//             InventoryItem::where('id', $item->inventory_item_id)
//                 ->decrement('current_stock', $item->quantity);
//         }

//         // Delete old items
//         $creditNote->items()->delete();

//         /* Recalculate subtotal & taxes */
//         $subtotal = 0; $cgst = 0; $sgst = 0; $igst = 0;
//         $companyState = strtolower(config('app.company_state', 'WB'));
//         $clientState  = strtolower($client->client_state);

//         foreach ($items as $row) {
//             $invoiceItem = $invoice->items->firstWhere('inventory_item_id', $row['id']);
//             $subtotal += $row['qty'] * $invoiceItem->unit_price;

//             $ratio = $row['qty'] / $invoiceItem->quantity;
//             if ($companyState === $clientState) {
//                 $cgst += ($invoiceItem->cgst ?? 0) * $ratio;
//                 $sgst += ($invoiceItem->sgst ?? 0) * $ratio;
//                 $gstType = 'cgst_sgst';
//             } else {
//                 $igst += ($invoiceItem->igst ?? 0) * $ratio;
//                 $gstType = 'igst';
//             }
//         }

//         $discount      = $request->discount ?? 0;
//         $taxableAmount = max($subtotal - $discount, 0);
//         $grandTotal    = $taxableAmount + $cgst + $sgst + $igst;

//         // Update credit note
//         $creditNote->update([
//             'subtotal'       => round($subtotal, 2),
//             'taxable_amount' => round($taxableAmount, 2),
//             'discount'       => round($discount, 2),
//             'cgst'           => round($cgst, 2),
//             'sgst'           => round($sgst, 2),
//             'igst'           => round($igst, 2),
//             'gst_type'       => $gstType,
//             'grand_total'    => round($grandTotal, 2),
//             'reason'         => $request->reason,
//         ]);

//         // Create new items + update stock
//         foreach ($items as $row) {
//             $invoiceItem = $invoice->items->firstWhere('inventory_item_id', $row['id']);
//             $ratio = $row['qty'] / $invoiceItem->quantity;

//             CreditNoteItem::create([
//                 'credit_note_id'    => $creditNote->id,
//                 'inventory_item_id' => $row['id'],
//                 'quantity'          => $row['qty'],
//                 'unit_price'        => $invoiceItem->unit_price,
//                 'taxable_amount'    => round($invoiceItem->taxable_amount * $ratio, 2),
//                 'cgst'              => round($invoiceItem->cgst * $ratio, 2),
//                 'sgst'              => round($invoiceItem->sgst * $ratio, 2),
//                 'igst'              => round($invoiceItem->igst * $ratio, 2),
//                 'gst_rate'          => $invoiceItem->gst_rate,
//                 'hsn_code'          => $invoiceItem->hsn_code,
//                 'total_price'       => round($row['qty'] * $invoiceItem->unit_price, 2),
//             ]);

//             InventoryItem::where('id', $row['id'])->increment('current_stock', $row['qty']);
//         }
//     });

//     return redirect()->route('admin.invoices.show', $creditNote->invoice_id)
//                      ->with('success', 'Credit Note updated successfully');
// }

public function update(Request $request, CreditNote $creditNote)
{
    $request->validate([
        'items'            => 'required|array',
        'items.*.id'       => 'required|exists:inventory_items,id',
        'items.*.qty'      => 'nullable|numeric|min:1',
        'items.*.selected' => 'nullable|boolean',
        'discount'         => 'nullable|numeric|min:0',
        'reason'           => 'required|string|max:255',
    ]);

    DB::transaction(function () use ($request, $creditNote) {

        /* ===============================
           BASIC SAFETY
        =============================== */
        if ($creditNote->status !== 'active') {
            throw new \Exception('Cancelled credit note cannot be edited.');
        }

        $invoice = $creditNote->invoice()->with('items.inventoryItem')->first();
        $client  = $creditNote->client;

        /* ===============================
           BLOCK EDIT IF INVOICE SETTLED
        =============================== */
        $paid = $invoice->payments()->sum('amount');
        $credited = $invoice->creditNotes()
            ->where('status', 'active')
            ->where('id', '!=', $creditNote->id)
            ->sum('grand_total');

        if (($paid + $credited) >= $invoice->grand_total) {
            throw new \Exception('Invoice already settled. Credit note cannot be edited.');
        }

        /* ===============================
           FILTER SELECTED ITEMS
        =============================== */
        $items = collect($request->items)
            ->filter(fn ($i) => !empty($i['selected']))
            ->map(fn ($i) => [
                'id'  => (int) $i['id'],
                'qty' => (int) ($i['qty'] ?? 0),
            ])
            ->filter(fn ($i) => $i['qty'] > 0)
            ->values();

        if ($items->isEmpty()) {
            throw new \Exception('Please select at least one item with quantity.');
        }

        /* ===============================
           PREVENT OVER CREDITING
        =============================== */
        foreach ($items as $row) {
            $invoiceItem = $invoice->items
                ->firstWhere('inventory_item_id', $row['id']);

            if (!$invoiceItem) {
                throw new \Exception('Item not found in invoice.');
            }

            $otherCredits = CreditNoteItem::join(
                    'credit_notes',
                    'credit_notes.id',
                    '=',
                    'credit_note_items.credit_note_id'
                )
                ->where('credit_notes.invoice_id', $invoice->id)
                ->where('credit_notes.status', 'active')
                ->where('credit_notes.id', '!=', $creditNote->id)
                ->where('credit_note_items.inventory_item_id', $row['id'])
                ->sum('credit_note_items.quantity');

            $remainingQty = $invoiceItem->quantity - $otherCredits;

            if ($row['qty'] > $remainingQty) {
                throw new \Exception("Credit qty exceeds remaining qty for {$invoiceItem->inventoryItem->name}");
            }
        }

        /* ===============================
           REVERSE OLD STOCK
        =============================== */
        foreach ($creditNote->items as $oldItem) {
            InventoryItem::where('id', $oldItem->inventory_item_id)
                ->decrement('current_stock', $oldItem->quantity);
        }

        $creditNote->items()->delete();

        /* ===============================
           GST INCLUSIVE RE-CALCULATION
        =============================== */
        $grossTotal = 0;
        $taxableAmount = 0;
        $cgst = $sgst = $igst = 0;

        $companyState = strtolower(config('app.company_state', 'wb'));
        $clientState  = strtolower($client->client_state);
        $gstType      = ($companyState === $clientState) ? 'cgst_sgst' : 'igst';

        foreach ($items as $row) {
            $invoiceItem = $invoice->items
                ->firstWhere('inventory_item_id', $row['id']);

            $ratio = $row['qty'] / $invoiceItem->quantity;

            $grossTotal += $row['qty'] * $invoiceItem->unit_price;
            $taxableAmount += $invoiceItem->taxable_amount * $ratio;

            if ($gstType === 'cgst_sgst') {
                $cgst += $invoiceItem->cgst * $ratio;
                $sgst += $invoiceItem->sgst * $ratio;
            } else {
                $igst += $invoiceItem->igst * $ratio;
            }
        }

        /* ===============================
           DISCOUNT (INCLUSIVE)
        =============================== */
        $discount = min($request->discount ?? 0, $grossTotal);

        if ($discount > 0) {
            $ratio = ($grossTotal - $discount) / $grossTotal;
            $taxableAmount *= $ratio;
            $cgst *= $ratio;
            $sgst *= $ratio;
            $igst *= $ratio;
        }

        $grandTotal = $grossTotal - $discount;

        /* ===============================
           UPDATE CREDIT NOTE
        =============================== */
        $creditNote->update([
            'subtotal'       => round($grossTotal, 2),
            'taxable_amount' => round($taxableAmount, 2),
            'discount'       => round($discount, 2),
            'cgst'           => round($cgst, 2),
            'sgst'           => round($sgst, 2),
            'igst'           => round($igst, 2),
            'gst_type'       => $gstType,
            'grand_total'    => round($grandTotal, 2),
            'reason'         => $request->reason,
        ]);

        /* ===============================
           RECREATE ITEMS + RESTOCK
        =============================== */
        foreach ($items as $row) {
            $invoiceItem = $invoice->items
                ->firstWhere('inventory_item_id', $row['id']);

            $ratio = $row['qty'] / $invoiceItem->quantity;

            CreditNoteItem::create([
                'credit_note_id'    => $creditNote->id,
                'inventory_item_id' => $row['id'],
                'quantity'          => $row['qty'],
                'unit_price'        => $invoiceItem->unit_price,
                'taxable_amount'    => round($invoiceItem->taxable_amount * $ratio, 2),
                'cgst'              => round($invoiceItem->cgst * $ratio, 2),
                'sgst'              => round($invoiceItem->sgst * $ratio, 2),
                'igst'              => round($invoiceItem->igst * $ratio, 2),
                'gst_rate'          => $invoiceItem->gst_rate,
                'hsn_code'          => $invoiceItem->hsn_code,
                'total_price'       => round($row['qty'] * $invoiceItem->unit_price, 2),
            ]);

            InventoryItem::where('id', $row['id'])
                ->increment('current_stock', $row['qty']);
        }

        /* ===============================
           UPDATE INVOICE PAYMENT STATUS
        =============================== */
        $paid = $invoice->payments()->sum('amount');
        $credited = $invoice->creditNotes()
            ->where('status', 'active')
            ->sum('grand_total');

        $due = max($invoice->grand_total - ($paid + $credited), 0);

        $invoice->update([
            'payment_status' => $due == 0
                ? 'paid'
                : (($paid > 0 || $credited > 0) ? 'partial' : 'unpaid')
        ]);
    });

    return redirect()
        ->route('admin.invoices.show', $creditNote->invoice_id)
        ->with('success', 'Credit Note updated successfully');
}



    /**
     * Cancel a credit note
     */
    // public function cancel(CreditNote $creditNote)
    // {
    //     DB::transaction(function () use ($creditNote) {

    //         if ($creditNote->status !== 'active') {
    //             abort(400, 'Credit note already cancelled.');
    //         }

    //         // Reverse stock
    //         foreach ($creditNote->items as $item) {
    //             InventoryItem::where('id', $item->inventory_item_id)
    //                 ->decrement('current_stock', $item->quantity);
    //         }

    //         // Mark as cancelled
    //         $creditNote->update(['status' => 'cancelled']);
    //     });

    //     return redirect()->back()->with('success', 'Credit note cancelled successfully');
    // }

    public function cancel(CreditNote $creditNote)
    {
        DB::transaction(function () use ($creditNote) {

            $creditNote->refresh();

            if ($creditNote->status !== 'active') {
                return; // prevents double reversal
            }

            /* ===============================
            REVERSE STOCK
            =============================== */
            foreach ($creditNote->items as $item) {
                InventoryItem::where('id', $item->inventory_item_id)
                    ->decrement('current_stock', $item->quantity);

                // Optional but excellent
                StockTransaction::create([
                    'inventory_item_id' => $item->inventory_item_id,
                    'type'              => 'OUT',
                    'quantity'          => $item->quantity,
                    'reference_type'    => 'credit_note',
                    'reference_id'      => $creditNote->id,
                    'note'              => 'Credit Note cancelled #' . $creditNote->credit_note_no,
                ]);
            }

            /* ===============================
            CANCEL CREDIT NOTE
            =============================== */
            $creditNote->update([
                'status' => 'cancelled'
            ]);

            /* ===============================
            UPDATE INVOICE PAYMENT STATUS
            =============================== */
            $invoice = $creditNote->invoice;

            $paid = $invoice->payments()->sum('amount');
            $credited = $invoice->creditNotes()
                ->where('status', 'active')
                ->sum('grand_total');

            $due = max($invoice->grand_total - ($paid + $credited), 0);

            $invoice->update([
                'payment_status' => $due == 0
                    ? 'paid'
                    : (($paid > 0 || $credited > 0) ? 'partial' : 'unpaid')
            ]);
        });

        return redirect()->back()
            ->with('success', 'Credit note cancelled successfully');
    }


    public function downloadPdf(CreditNote $creditNote)
    {
        // Load the Blade view
        $pdf = Pdf::loadView('admin.credit-notes.pdf', compact('creditNote'));

        $filename = 'CreditNote-' . $creditNote->credit_note_no . '.pdf';

        // Option 1: Download PDF
        return $pdf->download($filename);

        // Option 2: Display in browser
        // return $pdf->stream($filename);
    }
}
