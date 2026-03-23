<?php

namespace App\Services;

use App\Models\InventoryBatch;
use DB;
use Exception;

class FifoService
{
    public function consume($itemId, $qtyNeeded)
    {
        $totalCost = 0;
        $qtyToConsume = $qtyNeeded;

        /** ✅ Lock rows to prevent race condition */
        $batches = InventoryBatch::where('inventory_item_id', $itemId)
            ->where('remaining_quantity', '>', 0)
            ->orderBy('id') // FIFO
            ->lockForUpdate()
            ->get();

        foreach ($batches as $batch) {

            if ($qtyToConsume <= 0) {
                break;
            }

            $available = $batch->remaining_quantity;

            if ($available <= 0) {
                continue;
            }

            /** ✅ Consume Qty */
            $consumeQty = min($available, $qtyToConsume);

            /** ✅ Cost Calculation */
            $totalCost += $consumeQty * $batch->unit_cost;

            /** ✅ Reduce Batch */
            $batch->decrement('remaining_quantity', $consumeQty);

            $qtyToConsume -= $consumeQty;
        }

        /** 🚨 TRUE FIFO VALIDATION */
        if ($qtyToConsume > 0) {
            throw new Exception('Insufficient FIFO stock');
        }

        return [
            'total_cost' => $totalCost,
            'unit_cost'  => $totalCost / $qtyNeeded
        ];
    }
}
