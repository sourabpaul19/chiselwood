<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class Gstr1Export implements FromCollection, WithHeadings
{
    protected $invoices;

    public function __construct($invoices)
    {
        $this->invoices = $invoices;
    }

    public function collection()
    {
        $data = [];

        foreach($this->invoices as $invoice) {
            foreach($invoice->items as $item) {
                $data[] = [
                    'Invoice No'      => $invoice->invoice_no,
                    'Date'            => $invoice->created_at->format('Y-m-d'),
                    'Client Name'     => $invoice->client->name,
                    'Client GSTIN'    => $invoice->client->gstin ?? '',
                    'HSN/SAC'         => $item->inventoryItem->hsn_code ?? '',
                    'Item Name'       => $item->inventoryItem->name ?? 'Item',
                    'Quantity'        => $item->quantity,
                    'Rate'            => $item->price,
                    'Taxable Value'   => $item->taxable_amount,
                    'CGST'            => $item->cgst,
                    'SGST'            => $item->sgst,
                    'IGST'            => $item->igst,
                    'Invoice Type'    => 'B2B',
                ];
            }
        }

        return collect($data);
    }

    public function headings(): array
    {
        return [
            'Invoice No','Date','Client Name','Client GSTIN','HSN/SAC',
            'Item Name','Quantity','Rate','Taxable Value','CGST','SGST','IGST','Invoice Type'
        ];
    }
}
