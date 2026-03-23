<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\CreditNote;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\Gstr1Export;

class Gstr1Controller extends Controller
{
    public function index(Request $request)
    {
        $from = $request->from ?? now()->startOfMonth();
        $to   = $request->to ?? now()->endOfMonth();

        // Fetch invoices excluding cancelled
        $invoices = Invoice::with(['items.inventoryItem','client'])
            ->whereBetween('created_at', [$from, $to])
            ->where('status','!=','cancelled')
            ->get();

        return response()->json($invoices);
    }

    public function export(Request $request)
    {
        $from = $request->from ?? now()->startOfMonth();
        $to   = $request->to ?? now()->endOfMonth();

        $invoices = Invoice::with(['items.inventoryItem','client'])
            ->whereBetween('created_at', [$from, $to])
            ->where('status','!=','cancelled')
            ->get();

        return Excel::download(new Gstr1Export($invoices), 'GSTR1.xlsx');
    }

    public function report(Request $request)
    {
        $from = $request->from ?? now()->startOfMonth();
        $to   = $request->to ?? now()->endOfMonth();

        $invoices = Invoice::with(['items.inventoryItem','client'])
            ->whereBetween('created_at', [$from, $to])
            ->where('status', '!=', 'cancelled')
            ->get();

        $creditNotes = CreditNote::with(['items.inventoryItem','client'])
            ->whereBetween('created_at', [$from, $to])
            ->get();

        // B2B / B2C split
        $b2b = $invoices->filter(fn($i) => !empty($i->client->gstin));
        $b2c = $invoices->filter(fn($i) => empty($i->client->gstin));

        // Totals
        $summary = [
            'taxable' => $invoices->sum(fn($i) => $i->items->sum('taxable_amount')),
            'cgst'    => $invoices->sum(fn($i) => $i->items->sum('cgst')),
            'sgst'    => $invoices->sum(fn($i) => $i->items->sum('sgst')),
            'igst'    => $invoices->sum(fn($i) => $i->items->sum('igst')),
        ];



        $hsnSummary = [];

        foreach ($invoices as $invoice) {
            foreach ($invoice->items as $item) {

                $hsn = $item->inventoryItem->sku ?? 'NA';

                if (!isset($hsnSummary[$hsn])) {
                    $hsnSummary[$hsn] = [
                        'hsn'        => $hsn,
                        'desc'       => $item->inventoryItem->name ?? '',
                        'uqc'        => $item->inventoryItem->unit->short_name ?? '',
                        'qty'        => 0,
                        'taxable'    => 0,
                        'cgst'       => 0,
                        'sgst'       => 0,
                        'igst'       => 0,
                    ];
                }

                $hsnSummary[$hsn]['qty']      += $item->quantity;
                $hsnSummary[$hsn]['taxable'] += $item->taxable_amount;
                $hsnSummary[$hsn]['cgst']    += $item->cgst;
                $hsnSummary[$hsn]['sgst']    += $item->sgst;
                $hsnSummary[$hsn]['igst']    += $item->igst;
            }
        }

        $gstRateSummary = [];

        foreach ($invoices as $invoice) {
            foreach ($invoice->items as $item) {

                $rate = $item->gst_rate; // 5, 12, 18, 28

                if (!isset($gstRateSummary[$rate])) {
                    $gstRateSummary[$rate] = [
                        'rate'     => $rate,
                        'taxable'  => 0,
                        'cgst'     => 0,
                        'sgst'     => 0,
                        'igst'     => 0,
                        'totalTax' => 0,
                    ];
                }

                $gstRateSummary[$rate]['taxable'] += $item->taxable_amount;
                $gstRateSummary[$rate]['cgst']    += $item->cgst;
                $gstRateSummary[$rate]['sgst']    += $item->sgst;
                $gstRateSummary[$rate]['igst']    += $item->igst;
            }
        }

        /* Calculate total tax */
        foreach ($gstRateSummary as &$row) {
            $row['totalTax'] = $row['cgst'] + $row['sgst'] + $row['igst'];
        }

        return view('admin.reports.gstr1', compact(
            'from','to','b2b','b2c','creditNotes',
            'summary','hsnSummary','gstRateSummary'
        ));


    }
}
