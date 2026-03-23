<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Vendor;
use App\Models\InventoryItem;
use Illuminate\Support\Facades\DB;

class PurchaseOrderController extends Controller
{
    /* ===============================
       LIST
    =============================== */
    public function index()
    {
        $orders = PurchaseOrder::with('vendor')->latest()->get();
        return view('admin.purchase-orders.index', compact('orders'));
    }

    /* ===============================
       CREATE PAGE
    =============================== */
    public function create()
    {
        $vendors = Vendor::all();
        return view('admin.purchase-orders.create', compact('vendors'));
    }

    /* ===============================
       VENDOR WISE ITEMS (AJAX)
    =============================== */
    public function getVendorItems($vendorId)
    {
        $items = InventoryItem::where('vendor_id', $vendorId)->get();
        return response()->json($items);
    }

    /* ===============================
       STORE (GST INCLUDED RATE)
    =============================== */
    public function store(Request $request)
    {
        //dd($request->all());
        $request->validate([
            'vendor_id' => 'required|exists:vendors,id',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|exists:inventory_items,id',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($request) {

            $vendor = Vendor::findOrFail($request->vendor_id);

            //$companyState = config('app.company_state'); // set in config/app.php
            $companyState = setting('company_state');
            $gstType = ($vendor->vendor_state == $companyState) ? 'cgst_sgst' : 'igst';

            $po = PurchaseOrder::create([
                'po_number' => 'PO-' . now()->format('YmdHis'),
                'vendor_id' => $vendor->id,
                'order_date'    => now(),
                'status' => 'draft',
                'gst_type' => $gstType,
                'subtotal' => 0,
                'taxable_amount' => 0,
                'cgst' => 0,
                'sgst' => 0,
                'igst' => 0,
                'tax_amount' => 0,
                'total_amount' => 0,
                'created_by' => auth()->id()
            ]);

            $subtotal = 0;
            $taxableTotal = 0;
            $cgstTotal = 0;
            $sgstTotal = 0;
            $igstTotal = 0;

            foreach ($request->items as $line) {

                $item = InventoryItem::findOrFail($line['item_id']);

                $qty = $line['quantity'];
                $rate = $line['unit_price']; // GST INCLUDED
                $gstRate = $item->gst_rate ?? 0;

                $lineTotal = $qty * $rate; // Final amount (GST included)

                // Extract GST from inclusive rate
                if ($gstRate > 0) {
                    $taxableAmount = $lineTotal / (1 + ($gstRate / 100));
                    $gstAmount = $lineTotal - $taxableAmount;
                } else {
                    $taxableAmount = $lineTotal;
                    $gstAmount = 0;
                }

                // Split tax
                if ($gstType == 'intra') {
                    $cgst = $gstAmount / 2;
                    $sgst = $gstAmount / 2;
                    $igst = 0;
                } else {
                    $cgst = 0;
                    $sgst = 0;
                    $igst = $gstAmount;
                }

                $subtotal += $lineTotal;
                $taxableTotal += $taxableAmount;
                $cgstTotal += $cgst;
                $sgstTotal += $sgst;
                $igstTotal += $igst;

                PurchaseOrderItem::create([
                    'purchase_order_id' => $po->id,
                    'inventory_item_id' => $item->id,
                    'hsn' => $item->hsn,
                    'quantity' => $qty,
                    'unit_price' => $rate,
                    'gst_rate' => $gstRate,
                    'item_subtotal' => $lineTotal,
                    'taxable_amount' => $taxableAmount,
                    'gst_type' => $gstType,
                    'cgst' => $cgst,
                    'sgst' => $sgst,
                    'igst' => $igst,
                    'total' => $lineTotal,
                ]);
            }

            $taxAmount = $cgstTotal + $sgstTotal + $igstTotal;

            $po->update([
                'subtotal' => $subtotal,
                'taxable_amount' => $taxableTotal,
                'cgst' => $cgstTotal,
                'sgst' => $sgstTotal,
                'igst' => $igstTotal,
                'tax_amount' => $taxAmount,
                'total_amount' => $subtotal, // GST already included
            ]);
        });

        return redirect()
            ->route('admin.purchase-orders.index')
            ->with('success', 'Purchase Order Created Successfully');
    }

    /* ===============================
       SHOW
    =============================== */
    public function show($id)
    {
        $po = PurchaseOrder::with('items.item', 'vendor')->findOrFail($id);
        return view('admin.purchase-orders.show', compact('po'));
    }

    /* ===============================
       APPROVE
    =============================== */
    public function approve($id)
    {
        $po = PurchaseOrder::findOrFail($id);

        if ($po->status !== 'draft') {
            return back()->with('error', 'Only draft orders can be approved');
        }

        $po->update(['status' => 'approved']);

        return back()->with('success', 'Purchase Order Approved');
    }

    /* ===============================
       DESTROY
    =============================== */
    public function destroy($id)
    {
        $po = PurchaseOrder::findOrFail($id);

        if ($po->status !== 'draft') {
            return back()->with('error', 'Only draft orders can be deleted');
        }

        $po->items()->delete();
        $po->delete();

        return back()->with('success', 'Purchase Order Deleted');
    }
}
