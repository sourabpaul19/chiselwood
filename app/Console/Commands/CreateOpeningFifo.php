<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\InventoryItem;
use App\Models\FifoBatch;

class CreateOpeningFifo extends Command
{
    protected $signature = 'fifo:opening';
    protected $description = 'Create opening FIFO batches from current stock';

    public function handle()
    {
        $this->info('Creating Opening FIFO Batches...');

        InventoryItem::where('current_stock', '>', 0)
            ->chunk(100, function ($items) {

                foreach ($items as $item) {

                    FifoBatch::create([
                        'inventory_item_id' => $item->id,
                        'qty_remaining'     => $item->current_stock,
                        'unit_cost'         => $item->purchase_price,
                        'source_type'       => 'opening',
                        'source_id'         => null,
                    ]);

                    $this->line("✔ Item: {$item->name}");
                }
            });

        $this->info('✅ Opening FIFO Created Successfully');
    }
}
