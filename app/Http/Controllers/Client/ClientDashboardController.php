<?php

namespace App\Http\Controllers\client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\Project;
use App\Models\Client;
use App\Models\Payment;
use Barryvdh\DomPDF\Facade\Pdf; // or use your existing alias
use Illuminate\Support\Facades\DB;

class ClientDashboardController extends Controller
{
    //
    public function dashboard()
    {
        $user = auth()->user();

        // Example based on your system
        $invoices = DB::table('invoices')->where('client_id', $user->id)->count();
        $projects = DB::table('projects')->where('client_id', $user->id)->count();

        return view('client.dashboard', compact('user', 'invoices', 'projects'));
    }

    public function projects()
    {
        $user = auth()->user();

        $client = Client::where('user_id', $user->id)->first();

        if (!$client) {
            return "Client not found";
        }

        $projects = Project::where('client_id', $client->id)
            ->latest()
            ->get();

        return view('client.projects', compact('projects'));
    }

    public function invoices()
    {
        $user = auth()->user();

        $client = Client::where('user_id', $user->id)->first();

        if (!$client) {
            return "Client not found";
        }

        $invoices = Invoice::where('client_id', $client->id)
            ->latest()
            ->get();

        return view('client.invoices', compact('invoices'));
    }

    public function showReceipts($invoice_id)
    {
        // Fetch invoice details
        $invoice = Invoice::findOrFail($invoice_id);

        // Fetch all payments (receipts) for this invoice
        $receipts = Payment::where('invoice_id', $invoice_id)->get();

        return view('client.invoice.receipts', compact('invoice', 'receipts'));
    }

    public function pdf(Invoice $invoice)
    {
        // Load related data (client, items, payments) same as admin
        $invoice->load(['client', 'items.item', 'payments']);

        // Reuse the same view as admin
        $pdf = Pdf::loadView('admin.invoices.pdf', compact('invoice'))
                ->setPaper('A4');

        return $pdf->download('Invoice-' . $invoice->invoice_no . '.pdf');
    }

    public function downloadReceipt($payment_id)
    {
        $payment = Payment::with('invoice.client')->findOrFail($payment_id);

        // Correct ownership check
        if ($payment->invoice->client->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access.');
        }

        $pdf = Pdf::loadView('admin.payments.receipt-pdf', compact('payment'));

        return $pdf->download('Payment-Receipt-' . $payment->receipt_no . '.pdf');
    }
}
