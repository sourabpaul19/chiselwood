<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Client;
use Illuminate\Support\Facades\DB;

class ClientLedgerController extends Controller
{
    public function index(Client $client)
    {
        /*
        Ledger Query:
        Invoice  -> Debit
        Payment  -> Credit
        CreditNote -> Credit
        */

        $invoices = DB::table('invoices')
            ->where('client_id', $client->id)
            ->where('status', '!=', 'cancelled')
            ->select(
                'invoice_date as date',
                DB::raw("'Invoice' as type"),
                'invoice_no as reference',
                'grand_total as debit',
                DB::raw('0 as credit')
            );

        $payments = DB::table('payments')
            ->join('invoices', 'invoices.id', '=', 'payments.invoice_id')
            ->where('invoices.client_id', $client->id)
            ->select(
                'payment_date as date',
                DB::raw("'Payment' as type"),
                'payments.id as reference',
                DB::raw('0 as debit'),
                'payments.amount as credit'
            );

        $creditNotes = DB::table('credit_notes')
            ->where('client_id', $client->id)
            ->where('status', 'active')
            ->select(
                'credit_date as date',
                DB::raw("'Credit Note' as type"),
                'credit_note_no as reference',
                DB::raw('0 as debit'),
                'grand_total as credit'
            );

        $ledger = $invoices
            ->unionAll($payments)
            ->unionAll($creditNotes)
            ->orderBy('date')
            ->get();

        // Running balance
        $balance = 0;
        $ledger = $ledger->map(function ($row) use (&$balance) {
            $balance += ($row->debit - $row->credit);
            $row->balance = $balance;
            return $row;
        });

        return view('admin.clients.ledger', compact('client', 'ledger'));
    }
}
