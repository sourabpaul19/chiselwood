<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\CreditNote;
use App\Models\Expense;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use PDF;

class FinancialReportController extends Controller
{
    public function index(Request $request)
    {
        $from = $request->from;
        $to   = $request->to;

        // =========================
        // 1️⃣ TOTAL REVENUE
        // =========================
        $invoiceQuery = Invoice::where('is_final', 1)
            ->whereNull('cancelled_at');

        if ($from && $to) {
            $invoiceQuery->whereBetween('invoice_date', [$from, $to]);
        }

        $totalRevenue = $invoiceQuery->sum('grand_total');

        // =========================
        // 2️⃣ SALES RETURN (Credit Notes)
        // =========================
        $creditQuery = CreditNote::where('status', 'active');

        if ($from && $to) {
            $creditQuery->whereBetween('credit_date', [$from, $to]);
        }

        $totalReturns = $creditQuery->sum('grand_total');

        $netRevenue = $totalRevenue - $totalReturns;

        // =========================
        // 3️⃣ COGS (FIFO)
        // =========================
        $cogsQuery = DB::table('invoice_items')
            ->join('invoices', 'invoice_items.invoice_id', '=', 'invoices.id')
            ->where('invoices.is_final', 1)
            ->whereNull('invoices.cancelled_at');

        if ($from && $to) {
            $cogsQuery->whereBetween('invoices.invoice_date', [$from, $to]);
        }

        $totalCogs = $cogsQuery->sum('invoice_items.fifo_cost');

        // =========================
        // 4️⃣ EXPENSES
        // =========================
        $expenseQuery = Expense::query();

        if ($from && $to) {
            $expenseQuery->whereBetween('expense_date', [$from, $to]);
        }

        $totalExpenses = $expenseQuery->sum('amount');

        // =========================
        // 5️⃣ NET PROFIT
        // =========================
        $grossProfit = $netRevenue - $totalCogs;
        $netProfit   = $grossProfit - $totalExpenses;

        return view('admin.reports.financial_summary', compact(
            'from',
            'to',
            'totalRevenue',
            'totalReturns',
            'netRevenue',
            'totalCogs',
            'totalExpenses',
            'grossProfit',
            'netProfit'
        ));
    }

    public function monthlyProfit()
    {
        $monthly = \DB::table('invoices')
            ->selectRaw('
                YEAR(invoice_date) as year,
                MONTH(invoice_date) as month,
                SUM(grand_total) as revenue
            ')
            ->where('is_final', 1)
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        $cogs = \DB::table('invoice_items')
            ->join('invoices','invoice_items.invoice_id','=','invoices.id')
            ->selectRaw('
                YEAR(invoices.invoice_date) as year,
                MONTH(invoices.invoice_date) as month,
                SUM(invoice_items.fifo_cost) as cogs
            ')
            ->where('invoices.is_final',1)
            ->groupBy('year','month')
            ->get();

        return view('admin.reports.monthly_profit', compact('monthly','cogs'));
    }

    public function expenseByCategory()
    {
        $data = \DB::table('expenses')
            ->join('expense_categories','expenses.expense_category_id','=','expense_categories.id')
            ->select('expense_categories.name',
                \DB::raw('SUM(expenses.amount) as total'))
            ->groupBy('expense_categories.name')
            ->get();

        return view('admin.reports.expense_category', compact('data'));
    }

    public function taxSummary()
    {
        $tax = Invoice::where('is_final',1)
            ->selectRaw('
                SUM(cgst) as total_cgst,
                SUM(sgst) as total_sgst,
                SUM(igst) as total_igst
            ')
            ->first();

        return view('admin.reports.tax_summary', compact('tax'));
    }

    public function outstandingReceivables()
    {
        $data = Invoice::with('payments')
            ->where('is_final',1)
            ->get()
            ->map(function($invoice){

                $paid = $invoice->payments->sum('amount');
                $credited = $invoice->creditNotes()
                    ->where('status','active')
                    ->sum('grand_total');

                $due = $invoice->grand_total - ($paid + $credited);

                return [
                    'invoice_no' => $invoice->invoice_no,
                    'client_id' => $invoice->client_id,
                    'grand_total' => $invoice->grand_total,
                    'paid' => $paid,
                    'due' => $due
                ];
            });

        return view('admin.reports.outstanding', compact('data'));
    }
    public function cashFlow()
    {
        $inflow = Payment::sum('amount');

        $outflow = Expense::sum('amount');

        return view('admin.reports.cashflow', compact('inflow','outflow'));
    }
    public function printPL()
    {
        $data = $this->calculatePL(); // your profit calculation

        $pdf = PDF::loadView('admin.reports.pl_pdf', $data);

        return $pdf->download('profit_loss.pdf');
    }

    public function balanceSheet()
    {
        // =====================
        // CASH (Payments received)
        // =====================
        $cash = \App\Models\Payment::sum('amount');

        // =====================
        // INVENTORY VALUE
        // =====================
        $inventoryValue = \DB::table('inventory_batches')
            ->selectRaw('SUM(remaining_quantity * unit_cost) as total')
            ->value('total') ?? 0;

        // =====================
        // RECEIVABLES
        // =====================
        $receivables = \App\Models\Invoice::where('is_final',1)
            ->where('payment_status','!=','paid')
            ->sum('grand_total');

        // =====================
        // TOTAL ASSETS
        // =====================
        $totalAssets = $cash + $inventoryValue + $receivables;

        // =====================
        // PAYABLES (PO total)
        // =====================
        $payables = \App\Models\PurchaseOrder::sum('total_amount');

        // =====================
        // EXPENSES
        // =====================
        $expenses = \App\Models\Expense::sum('amount');

        // =====================
        // REVENUE
        // =====================
        $revenue = \App\Models\Invoice::where('is_final',1)
            ->sum('grand_total');

        // =====================
        // COGS
        // =====================
        $cogs = \DB::table('invoice_items')
            ->join('invoices','invoice_items.invoice_id','=','invoices.id')
            ->where('invoices.is_final',1)
            ->sum('invoice_items.fifo_cost');

        $netProfit = $revenue - $cogs - $expenses;

        // =====================
        // EQUITY
        // =====================
        $equity = $netProfit;

        return view('admin.reports.balance_sheet', compact(
            'cash',
            'inventoryValue',
            'receivables',
            'totalAssets',
            'payables',
            'equity'
        ));
    }

}
