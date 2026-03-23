<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;

class ProfitLossExport implements FromArray
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function array(): array
    {
        return [
            ['Revenue', $this->data['revenue']],
            ['Returns', $this->data['returns']],
            ['Net Revenue', $this->data['netRevenue']],
            ['COGS', $this->data['cogs']],
            ['Expenses', $this->data['expenses']],
            ['Net Profit', $this->data['netProfit']],
        ];
    }
}
