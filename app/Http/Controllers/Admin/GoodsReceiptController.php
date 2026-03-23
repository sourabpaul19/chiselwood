<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\GoodsReceipt;
use App\Models\GoodsReceiptItem;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\InventoryItem;
use App\Models\StockTransaction;
use App\Models\InventoryBatch;
use DB;

class GoodsReceiptController extends Controller
{
    // public function create()
    // {
    //     $orders = PurchaseOrder::whereIn('status', [
    //         'approved',
    //         'partially_received'
    //     ])->with('items.inventoryItem')->get();

    //     return view('admin.purchase-receipts.create', compact('orders'));
    // }

    public function create()
    {
        $orders = PurchaseOrder::whereIn('status', [
                'approved',
                'partially_received'
            ])
            ->with([
                'vendor',                 // ✅ LOAD VENDOR
                'items.inventoryItem'     // ✅ LOAD ITEMS
            ])
            ->get();

        return view('admin.purchase-receipts.create', compact('orders'));
    }


    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'purchase_order_id' => 'required|exists:purchase_orders,id',
    //         'items' => 'required|array'
    //     ]);

    //     DB::transaction(function () use ($request) {

    //         $po = PurchaseOrder::lockForUpdate()
    //             ->findOrFail($request->purchase_order_id);

    //         /* ===============================
    //          * CREATE GOODS RECEIPT
    //          * =============================== */

    //         $receipt = GoodsReceipt::create([
    //             'receipt_number'    => 'GR-' . now()->format('YmdHis'),
    //             'purchase_order_id' => $po->id,
    //             'vendor_id'         => $po->vendor_id ?? null, // if exists
    //             'receipt_date'      => now(),
    //             'created_by'        => auth()->id(),
    //             'status'            => 'posted' // optional if you have status column
    //         ]);

    //         $hasValidItem = false;

    //         foreach ($request->items as $line) {

    //             if (!isset($line['received_qty']) || $line['received_qty'] <= 0) {
    //                 continue;
    //             }

    //             $poItem = PurchaseOrderItem::lockForUpdate()
    //                 ->findOrFail($line['po_item_id']);

    //             /* ===============================
    //              * PENDING QTY PROTECTION
    //              * =============================== */

    //             $pendingQty = $poItem->quantity - $poItem->received_quantity;

    //             if ($line['received_qty'] > $pendingQty) {
    //                 abort(400, 'Over receiving not allowed');
    //             }

    //             $inventoryItem = InventoryItem::lockForUpdate()
    //                 ->findOrFail($poItem->inventory_item_id);

    //             $hasValidItem = true;

    //             /* ===============================
    //              * AUTO SELLING PRICE UPDATE
    //              * =============================== */

    //             $oldCost  = $inventoryItem->purchase_price;
    //             $oldPrice = $inventoryItem->selling_price;

    //             $marginPercent = 0;

    //             if ($oldCost > 0) {
    //                 $marginPercent = (($oldPrice - $oldCost) / $oldCost) * 100;
    //             }

    //             $newCost = $poItem->unit_price;
    //             $newSellingPrice = $newCost + ($newCost * $marginPercent / 100);

    //             $inventoryItem->update([
    //                 'purchase_price' => $newCost,
    //                 'selling_price'  => $newSellingPrice
    //             ]);

    //             /* ===============================
    //              * SAVE RECEIPT ITEM
    //              * =============================== */

    //             GoodsReceiptItem::create([
    //                 'goods_receipt_id'       => $receipt->id,
    //                 'purchase_order_item_id' => $poItem->id,
    //                 'inventory_item_id'      => $inventoryItem->id, // add if column exists
    //                 'received_quantity'      => $line['received_qty'],
    //                 'unit_cost'              => $newCost, // add if column exists
    //                 'selling_price'          => $newSellingPrice, // add if column exists
    //                 'total'                  => $line['received_qty'] * $newCost // add if exists
    //             ]);

    //             $poItem->increment('received_quantity', $line['received_qty']);

    //             /* ===============================
    //              * UPDATE STOCK
    //              * =============================== */

    //             $inventoryItem->increment('stocks', $line['received_qty']);

    //             StockTransaction::create([
    //                 'inventory_item_id' => $inventoryItem->id,
    //                 'type'              => 'IN',
    //                 'quantity'          => $line['received_qty'],
    //                 'reference_type'    => 'goods_receipt',
    //                 'reference_id'      => $receipt->id,
    //             ]);

    //             /* ===============================
    //              * CREATE FIFO BATCH
    //              * =============================== */

    //             InventoryBatch::create([
    //                 'inventory_item_id'  => $inventoryItem->id,
    //                 'quantity'           => $line['received_qty'],
    //                 'remaining_quantity' => $line['received_qty'],
    //                 'unit_cost'          => $newCost,
    //                 'selling_price'      => $newSellingPrice,
    //                 'reference_type'     => 'goods_receipt',
    //                 'reference_id'       => $receipt->id,
    //             ]);
    //         }

    //         if (!$hasValidItem) {
    //             abort(400, 'No valid received quantity provided.');
    //         }

    //         $this->updateStatus($po->id);
    //     });

    //     return back()->with('success', 'Goods Receipt Created & Stock Updated Successfully');
    // }

    public function index()
    {
        $receipts = GoodsReceipt::with('purchaseOrder', 'vendor')
            ->latest()
            ->paginate(15);

        return view('admin.purchase-receipts.index', compact('receipts'));
    }

    public function show($id)
    {
        $receipt = GoodsReceipt::with([
            'vendor',
            'purchaseOrder',
            'items.inventoryItem'
        ])->findOrFail($id);

        return view('admin.purchase-receipts.show', compact('receipt'));
    }



    public function store(Request $request)
    {
        $request->validate([
            'purchase_order_id' => 'required|exists:purchase_orders,id',
            'items' => 'required|array'
        ]);

        DB::transaction(function () use ($request) {

            $po = PurchaseOrder::lockForUpdate()
                ->findOrFail($request->purchase_order_id);

            $receipt = GoodsReceipt::create([
                'receipt_number'    => 'GR-' . now()->format('YmdHis'),
                'purchase_order_id' => $po->id,
                'vendor_id'         => $po->vendor_id,
                'receipt_date'      => now(),
                'created_by'        => auth()->id(),
            ]);

            foreach ($request->items as $line) {

                if (empty($line['received_qty']) || $line['received_qty'] <= 0) {
                    continue;
                }

                $poItem = PurchaseOrderItem::lockForUpdate()
                    ->findOrFail($line['po_item_id']);

                $pendingQty = $poItem->quantity - $poItem->received_quantity;

                if ($line['received_qty'] > $pendingQty) {
                    abort(400, 'Over receiving not allowed');
                }

                $inventoryItem = InventoryItem::lockForUpdate()
                    ->findOrFail($poItem->inventory_item_id);

                $newCost = $line['unit_price'];

                if (!empty($line['selling_price'])) {
                    $newSellingPrice = $line['selling_price'];
                } else {
                    $oldCost  = $inventoryItem->purchase_price;
                    $oldPrice = $inventoryItem->selling_price;

                    $marginPercent = 0;

                    if ($oldCost > 0) {
                        $marginPercent = (($oldPrice - $oldCost) / $oldCost) * 100;
                    }

                    $newSellingPrice = $newCost + ($newCost * $marginPercent / 100);
                }

                $inventoryItem->update([
                    'purchase_price' => $newCost,
                    'selling_price'  => $newSellingPrice
                ]);

                GoodsReceiptItem::create([
                    'goods_receipt_id'       => $receipt->id,
                    'purchase_order_item_id' => $poItem->id,
                    'inventory_item_id'      => $inventoryItem->id,
                    'received_quantity'      => $line['received_qty'],
                    'unit_cost'              => $newCost,
                    'selling_price'          => $newSellingPrice,
                    'total'                  => $line['received_qty'] * $newCost
                ]);

                $poItem->increment('received_quantity', $line['received_qty']);

                $inventoryItem->increment('stocks', $line['received_qty']);

                StockTransaction::create([
                    'inventory_item_id' => $inventoryItem->id,
                    'type'              => 'IN',
                    'quantity'          => $line['received_qty'],
                    'reference_type'    => 'goods_receipt',
                    'reference_id'      => $receipt->id,
                    'note'              => 'Goods Receipt #' . $receipt->receipt_number,
                ]);

                InventoryBatch::create([
                    'inventory_item_id'  => $inventoryItem->id,
                    'quantity'           => $line['received_qty'],
                    'remaining_quantity' => $line['received_qty'],
                    'unit_cost'          => $newCost,
                    'selling_price'      => $newSellingPrice,
                    'reference_type'     => 'goods_receipt',
                    'reference_id'       => $receipt->id,
                ]);
            }

            $this->updateStatus($po->id);
        });

        return back()->with('success', 'Goods Receipt Created Successfully');
    }

    /* ===============================
     * UPDATE PURCHASE ORDER STATUS
     * =============================== */

    private function updateStatus($poId)
    {
        $po = PurchaseOrder::with('items')->find($poId);

        $totalOrdered  = $po->items->sum('quantity');
        $totalReceived = $po->items->sum('received_quantity');

        if ($totalReceived == 0) {
            $po->status = 'approved';
        } elseif ($totalReceived < $totalOrdered) {
            $po->status = 'partially_received';
        } else {
            $po->status = 'received';
        }

        $po->save();
    }
}
