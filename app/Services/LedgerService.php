<?php 

// app/Services/LedgerService.php
namespace App\Services;

use App\Models\ClientLedger;

class LedgerService
{
    public static function recalculate($clientId)
    {
        $balance = 0;

        $entries = ClientLedger::where('client_id', $clientId)
            ->orderBy('date')
            ->orderBy('id')
            ->get();

        foreach ($entries as $entry) {
            $balance += $entry->debit;
            $balance -= $entry->credit;

            $entry->update(['balance' => $balance]);
        }
    }
}
