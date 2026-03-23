<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\CreditNote;
use App\Models\Expense;
use App\Models\StockTransaction;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ProfitLossExport;
use DB;

class AnalyticsController extends Controller
{

    /*
    |--------------------------------------------------------------------------
    | COMMON DATE FILTER
    |--------------------------------------------------------------------------
    */

    private function getDateRange(Request $request)
    {
        $from = $request->from;
        $to   = $request->to;

        // Quick buttons
        if ($request->quick) {

            if ($request->quick == 'today') {
                $from = Carbon::today()->toDateString();
                $to   = Carbon::today()->toDateString();
            }

            if ($request->quick == 'month') {
                $from = Carbon::now()->startOfMonth()->toDateString();
                $to   = Carbon::now()->endOfMonth()->toDateString();
            }

            if ($request->quick == 'year') {
                $from = Carbon::now()->startOfYear()->toDateString();
                $to   = Carbon::now()->endOfYear()->toDateString();
            }
        }

        // Financial Year (India Apr–Mar)
        if ($request->financial_year) {
            $year = $request->financial_year;
            $from = $year . '-04-01';
            $to   = ($year + 1) . '-03-31';
        }

        // Default = current month
        if (!$from || !$to) {
            $from = Carbon::now()->startOfMonth()->toDateString();
            $to   = Carbon::now()->endOfMonth()->toDateString();
        }

        return [$from, $to];
    }

    /*
    |--------------------------------------------------------------------------
    | PROFIT & LOSS
    |--------------------------------------------------------------------------
    */

    public function profitLoss(Request $request)
    {
        [$from, $to] = $this->getDateRange($request);

        $revenue = DB::table('invoices')
            ->where('is_final',1)
            ->whereBetween('invoice_date',[$from,$to])
            ->sum('grand_total');

        $returns = DB::table('credit_notes')
            ->where('status','active')
            ->whereBetween('credit_date',[$from,$to])
            ->sum('grand_total');

        $netRevenue = $revenue - $returns;

        // FIXED COGS (quantity × fifo_cost)
        $cogs = DB::table('invoice_items')
            ->join('invoices','invoice_items.invoice_id','=','invoices.id')
            ->where('invoices.is_final',1)
            ->whereBetween('invoices.invoice_date',[$from,$to])
            ->selectRaw('SUM(invoice_items.quantity * invoice_items.fifo_cost) as total')
            ->value('total') ?? 0;

        $expenses = DB::table('expenses')
            ->whereBetween('expense_date',[$from,$to])
            ->sum('amount');

        $grossProfit = $netRevenue - $cogs;
        $netProfit   = $grossProfit - $expenses;

        return view('admin.reports.profit_loss', compact(
            'from','to',
            'revenue','returns','netRevenue',
            'cogs','expenses','grossProfit','netProfit'
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | REVENUE CHART
    |--------------------------------------------------------------------------
    */

    public function revenueChart(Request $request)
    {
        [$from, $to] = $this->getDateRange($request);

        $data = DB::table('invoices')
            ->selectRaw('MONTH(invoice_date) as month, SUM(grand_total) as total')
            ->where('is_final',1)
            ->whereBetween('invoice_date',[$from,$to])
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return view('admin.reports.revenue_chart', compact('data','from','to'));
    }

    /*
    |--------------------------------------------------------------------------
    | PROFIT BY PRODUCT
    |--------------------------------------------------------------------------
    */

    public function profitByProduct(Request $request)
    {
        [$from, $to] = $this->getDateRange($request);

        $data = DB::table('invoice_items')
            ->join('invoices','invoice_items.invoice_id','=','invoices.id')
            ->join('inventory_items','invoice_items.inventory_item_id','=','inventory_items.id')
            ->where('invoices.is_final',1)
            ->whereBetween('invoices.invoice_date',[$from,$to])
            ->select(
                'inventory_items.name',
                DB::raw('SUM(invoice_items.total_price) as revenue'),
                DB::raw('SUM(invoice_items.quantity * invoice_items.fifo_cost) as cost'),
                DB::raw('SUM(invoice_items.profit) as profit')
            )
            ->groupBy('inventory_items.name')
            ->get();

        return view('admin.reports.profit_product', compact('data','from','to'));
    }

    /*
    |--------------------------------------------------------------------------
    | PROFIT BY CLIENT
    |--------------------------------------------------------------------------
    */

    public function profitByClient(Request $request)
    {
        [$from, $to] = $this->getDateRange($request);

        $data = DB::table('invoices')
            ->join('invoice_items','invoices.id','=','invoice_items.invoice_id')
            ->where('invoices.is_final',1)
            ->whereBetween('invoices.invoice_date',[$from,$to])
            ->select(
                'invoices.client_id',
                DB::raw('SUM(invoice_items.total_price) as revenue'),
                DB::raw('SUM(invoice_items.quantity * invoice_items.fifo_cost) as cost'),
                DB::raw('SUM(invoice_items.profit) as profit')
            )
            ->groupBy('invoices.client_id')
            ->get();

        return view('admin.reports.profit_client', compact('data','from','to'));
    }

    /*
    |--------------------------------------------------------------------------
    | GST WISE REVENUE
    |--------------------------------------------------------------------------
    */

    public function gstWiseRevenue(Request $request)
    {
        [$from, $to] = $this->getDateRange($request);

        $data = DB::table('invoice_items')
            ->join('invoices','invoice_items.invoice_id','=','invoices.id')
            ->where('invoices.is_final',1)
            ->whereBetween('invoices.invoice_date',[$from,$to])
            ->select(
                'invoice_items.gst_rate',
                DB::raw('SUM(invoice_items.taxable_amount) as taxable'),
                DB::raw('SUM(invoice_items.cgst) as cgst'),
                DB::raw('SUM(invoice_items.sgst) as sgst'),
                DB::raw('SUM(invoice_items.igst) as igst')
            )
            ->groupBy('invoice_items.gst_rate')
            ->get();

        return view('admin.reports.gst_wise', compact('data','from','to'));
    }


    public function yearlyComparison()
    {
        $currentYear = now()->year;
        $lastYear = $currentYear - 1;

        $thisYear = \App\Models\Invoice::whereYear('invoice_date',$currentYear)
            ->where('is_final',1)
            ->sum('grand_total');

        $previousYear = \App\Models\Invoice::whereYear('invoice_date',$lastYear)
            ->where('is_final',1)
            ->sum('grand_total');

        $growth = 0;

        if($previousYear > 0){
            $growth = (($thisYear - $previousYear)/$previousYear) * 100;
        }

        return view('admin.reports.year_comparison', compact(
            'thisYear','previousYear','growth'
        ));
    }

    public function exportProfitLoss(Request $request)
    {
        [$from, $to] = $this->getDateRange($request);

        $revenue = Invoice::whereBetween('invoice_date', [$from, $to])
            ->where('is_final', 1)
            ->sum('grand_total');

        $returns = CreditNote::whereBetween('credit_date', [$from, $to])
            ->sum('grand_total');

        $cogs = InvoiceItem::whereHas('invoice', function ($q) use ($from, $to) {
                $q->whereBetween('invoice_date', [$from, $to])
                  ->where('is_final', 1);
            })
            ->selectRaw('SUM(quantity * fifo_cost) as total')
            ->value('total') ?? 0;

        $expenses = Expense::whereBetween('expense_date', [$from, $to])
            ->sum('amount');

        $data = [
            'revenue'    => $revenue,
            'returns'    => $returns,
            'netRevenue' => $revenue - $returns,
            'cogs'       => $cogs,
            'expenses'   => $expenses,
            'netProfit'  => ($revenue - $returns - $cogs - $expenses),
        ];

        return Excel::download(
            new ProfitLossExport($data),
            'profit_loss.xlsx'
        );
    }


    public function inventoryValuation()
    {
        $data = \DB::table('inventory_batches')
            ->join('inventory_items','inventory_batches.inventory_item_id','=','inventory_items.id')
            ->select(
                'inventory_items.name',
                \DB::raw('SUM(inventory_batches.remaining_quantity) as total_qty'),
                \DB::raw('SUM(inventory_batches.remaining_quantity * inventory_batches.unit_cost) as stock_value')
            )
            ->where('inventory_batches.remaining_quantity','>',0)
            ->groupBy('inventory_items.name')
            ->get();

        $totalValue = $data->sum('stock_value');

        return view('admin.reports.inventory_valuation', compact('data','totalValue'));
    }

    public function agingReport()
    {
        $today = now();

        $data = \DB::table('invoices')
            ->where('is_final',1)
            ->where('payment_status','!=','paid')
            ->select(
                'client_id',
                'invoice_no',
                'invoice_date',
                'grand_total',
                \DB::raw("DATEDIFF('$today', invoice_date) as days_due")
            )
            ->get()
            ->map(function($row){
                if($row->days_due <= 30){
                    $row->bucket = '0-30';
                }elseif($row->days_due <= 60){
                    $row->bucket = '31-60';
                }elseif($row->days_due <= 90){
                    $row->bucket = '61-90';
                }else{
                    $row->bucket = '90+';
                }
                return $row;
            });

        return view('admin.reports.aging_report', compact('data'));
    }


    public function movementReport($itemId)
    {
        $item = \DB::table('inventory_items')->find($itemId);

        $transactions = \DB::table('stock_transactions')
            ->where('inventory_item_id', $itemId)
            ->orderBy('created_at', 'asc')
            ->get();

        $balance = 0;

        foreach ($transactions as $t) {
            if ($t->type == 'IN') {
                $balance += $t->quantity;
            } else {
                $balance -= $t->quantity;
            }

            $t->running_balance = $balance;
        }

        return view('admin.reports.inventory_movement', compact('item', 'transactions'));
    }

    public function stockAging()
    {
        $items = \DB::table('inventory_items')->get();
        $report = [];

        foreach ($items as $item) {

            $transactions = \DB::table('stock_transactions')
                ->where('inventory_item_id',$item->id)
                ->where('type','IN')
                ->get();

            foreach($transactions as $t){

                $days = now()->diffInDays(\Carbon\Carbon::parse($t->created_at));

                $report[] = [
                    'item' => $item->name,
                    'quantity' => $t->quantity,
                    'days' => $days
                ];
            }
        }

        return view('admin.reports.stock_aging',compact('report'));
    }
    public function lowStock()
    {
        $items = \DB::table('inventory_items')
            ->whereColumn('stocks','<=','minimum_stock')
            ->get();

        return view('admin.reports.low_stock',compact('items'));
    }

    public function deadStock()
    {
        $items = \DB::table('inventory_items')->get();
        $dead = [];

        foreach($items as $item){

            $lastSale = \DB::table('stock_transactions')
                ->where('inventory_item_id',$item->id)
                ->where('type','OUT')
                ->latest('created_at')
                ->first();

            if(!$lastSale || now()->diffInDays($lastSale->created_at) > 90){
                $dead[] = $item;
            }
        }

        return view('admin.reports.dead_stock',compact('dead'));
    }
    public function dailyClosing(Request $request)
    {
        $date = $request->date ?? today();

        $items = \DB::table('inventory_items')->get();
        $closing = [];

        foreach($items as $item){

            $in = \DB::table('stock_transactions')
                ->where('inventory_item_id',$item->id)
                ->where('type','IN')
                ->whereDate('created_at','<=',$date)
                ->sum('quantity');

            $out = \DB::table('stock_transactions')
                ->where('inventory_item_id',$item->id)
                ->where('type','OUT')
                ->whereDate('created_at','<=',$date)
                ->sum('quantity');

            $closing[] = [
                'item'=>$item->name,
                'stock'=>$in-$out
            ];
        }

        return view('admin.reports.daily_closing',compact('closing','date'));
    }



}
