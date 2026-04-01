<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InventoryItem;
use App\Models\InventoryCategory;
use App\Models\InventoryUnit;
use App\Models\InventoryBrand;
use App\Models\InventoryBatch;
use App\Models\StockTransaction;
//use App\Models\Vendor;
use App\Models\InventoryVendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\FifoBatch;


class InventoryItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $items = InventoryItem::with([
                'categories:id,name',
                'subCategories:id,name',
                'brand:id,name',
                'unit:id,name,short_name'
            ])
            ->when($request->status, function ($q) use ($request) {
                $q->where('status', $request->status);
            })

            ->when($request->brand_id, fn($q) =>
            $q->where('brand_id',$request->brand_id)
        )

        // 🔹 CATEGORY FILTER (type = category)
        ->when($request->filled('category_id'), function ($q) use ($request) {
            $q->whereHas('categories', function ($cat) use ($request) {
                $cat->where('inventory_categories.id', $request->category_id)
                    ->wherePivot('type', 'category');
            });
        })

        // 🔹 SUB CATEGORY FILTER (type = sub_category)
        ->when($request->filled('sub_category_id'), function ($q) use ($request) {
            $q->whereHas('categories', function ($sub) use ($request) {
                $sub->where('inventory_categories.id', $request->sub_category_id)
                    ->wherePivot('type', 'sub_category');
            });
        })

        ->when($request->search, function ($q) use ($request) {
            $q->where(function ($sub) use ($request) {
                $sub->where('name', 'like', "%{$request->search}%")
                    ->orWhere('sku', 'like', "%{$request->search}%");
            });
        })
        ->latest()
        ->paginate(10);

        // ✅ ADD THESE (THIS WAS MISSING)
        $brands = InventoryBrand::where('status','active')->get();
        $categories = InventoryCategory::whereNull('parent_id')->get();
        $subCategories = InventoryCategory::whereNotNull('parent_id')->get();
        $vendors = InventoryVendor::all();

        return view('admin.inventory-items.index', compact(
            'items',
            'categories',
            'brands',
            'subCategories',
            'vendors'
        ));
    }


    /**
     * Show create form
     */
    // public function create()
    // {
    //     $categories = InventoryCategory::whereNull('parent_id')
    //         ->with('children')
    //         ->where('status', 'active')
    //         ->orderBy('name')
    //         ->get();

    //     $units = InventoryUnit::where('status', 'active')
    //         ->orderBy('name')
    //         ->get();

    //     return view('admin.inventory-items.create', compact('categories', 'units'));
    // }


    public function create()
    {
        return view('admin.inventory-items.create', [
            'units' => InventoryUnit::where('status', 'active')->get(),

            'brands' => InventoryBrand::where('status','active')->get(),

            // Parent categories only
            'categories' => InventoryCategory::whereNull('parent_id')
                                ->where('status', 'active')
                                ->get(),

            // Child categories
            'subCategories' => InventoryCategory::whereNotNull('parent_id')
                                    ->where('status', 'active')
                                    ->get(),
                                    'vendors' => InventoryVendor::all(),
        ]);
    }
    public function store(Request $request)
    {

        $validated = $request->validate([
            'name'               => 'required|string|max:255',
            'sku'                => 'required|string|max:100|unique:inventory_items,sku',
            'unit_id'            => 'required|exists:inventory_units,id',
            'vendor_id'          => 'nullable|exists:inventory_vendors,id',

            'category_ids'       => 'required|array|min:1',
            'category_ids.*'     => 'exists:inventory_categories,id',

            'sub_category_ids'   => 'nullable|array',
            'sub_category_ids.*' => 'exists:inventory_categories,id',

            'brand_id'           => 'required|exists:inventory_brands,id',

            'description'        => 'nullable|string',
            'stocks'      => 'nullable|numeric|min:0',
            'minimum_stock'      => 'nullable|numeric|min:5',

            'purchase_price'     => 'nullable|numeric|min:0',
            'selling_price'      => 'nullable|numeric|min:0',

            'status'             => 'required|in:active,inactive',
            'gst_rate'           => 'required|numeric|min:0|max:28',

            'discount_type'      => 'required|in:percent,flat',
            'discount_value'     => 'required|numeric|min:0',
        ]);

        if (
            $validated['discount_type'] === 'percent' &&
            $validated['discount_value'] > 100
        ) {
            return back()
                ->withInput()
                ->withErrors([
                    'discount_value' => 'Discount percentage cannot exceed 100%'
                ]);
        }

        DB::beginTransaction();

        try {

            $stocks = $validated['stocks'] ?? 0;

            /** ✅ CREATE ITEM */
            $item = InventoryItem::create([
                'name'           => $validated['name'],
                'sku'            => strtoupper($validated['sku']),
                'unit_id'        => $validated['unit_id'],
                'brand_id'       => $validated['brand_id'],
                'description'    => $validated['description'] ?? null,
                'vendor_id'      => $validated['vendor_id'] ?? null,

                // ✅ CRITICAL FIX
                'stocks'  => $stocks,

                'purchase_price' => $validated['purchase_price'] ?? 0,
                'selling_price'  => $validated['selling_price'] ?? 0,

                'status'         => $validated['status'],
                'gst_rate'       => $validated['gst_rate'],
                'minimum_stock'    => $validated['minimum_stock'],

                'discount_type'  => $validated['discount_type'],
                'discount_value' => $validated['discount_value'],
            ]);

            /** ✅ CREATE FIFO OPENING LAYER */
            if ($stocks > 0) {

                InventoryBatch::create([
                    'inventory_item_id'  => $item->id,
                    'quantity'           => $stocks,
                    'remaining_quantity' => $stocks,
                    'unit_cost'          => $item->purchase_price,
                    'selling_price'      => $item->selling_price,
                    'reference_type'     => 'opening',
                    'reference_id'       => $item->id,
                ]);

                // ✅ VERY IMPORTANT: Create Stock Transaction (IN)
                StockTransaction::create([
                    'inventory_item_id' => $item->id,
                    'type'              => 'IN',
                    'quantity'          => $stocks,
                    'reference_type'    => 'opening',
                    'reference_id'      => $item->id,
                    'note'              => 'Opening Stock',
                ]);
            }

            /** ✅ ATTACH CATEGORIES */
            // $item->categories()->sync(
            //     collect($validated['category_ids'])
            //         ->mapWithKeys(fn ($id) => [$id => ['type' => 'category']])
            //         ->toArray()
            // );

            // /** ✅ ATTACH SUB-CATEGORIES */
            // if (!empty($validated['sub_category_ids'])) {
            //     $item->subCategories()->sync(
            //         collect($validated['sub_category_ids'])
            //             ->mapWithKeys(fn ($id) => [$id => ['type' => 'sub_category']])
            //             ->toArray()
            //     );
            // }
            $item->categories()->detach();

            /** Attach Main Categories */
            foreach ($validated['category_ids'] as $id) {
                $item->categories()->attach($id, [
                    'type' => 'category'
                ]);
            }

            /** Attach Sub Categories */
            if (!empty($validated['sub_category_ids'])) {
                foreach ($validated['sub_category_ids'] as $id) {
                    $item->categories()->attach($id, [
                        'type' => 'sub_category'
                    ]);
                }
            }

            DB::commit();

            return redirect()
                ->route('admin.inventory-items.index')
                ->with('success', 'Inventory item created successfully');

        } catch (\Exception $e) {

            DB::rollBack();

            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    public function edit(InventoryItem $inventoryItem)
    {
        $units = InventoryUnit::select('id','name','short_name')->get();

        $brands = InventoryBrand::where('status','active')->get();

        $categories = InventoryCategory::whereNull('parent_id')->get();

        $vendors = InventoryVendor::all();

        // load already attached relations
        $inventoryItem->load(['categories:id', 'subCategories:id']);

        // preload subcategories for already selected categories
        $subCategories = InventoryCategory::whereIn(
            'parent_id',
            $inventoryItem->categories->pluck('id')
        )->get();

        return view(
            'admin.inventory-items.edit',
            compact('inventoryItem', 'units', 'categories', 'brands', 'vendors', 'subCategories')
        );
    }

    // public function update(Request $request, InventoryItem $inventoryItem)
    // {
    //     $request->validate([
    //         'name'              => 'required|string|max:255',
    //         'sku'               => 'required|string|max:100|unique:inventory_items,sku,' . $inventoryItem->id,
    //         'unit_id'           => 'required|exists:inventory_units,id',
    //         'brand_id'          => 'required|exists:inventory_brands,id',
    //         'category_ids'      => 'required|array|min:1',
    //         'category_ids.*'    => 'exists:inventory_categories,id',
    //         'sub_category_ids'  => 'nullable|array',
    //         'sub_category_ids.*'=> 'exists:inventory_categories,id',
    //         'opening_stock'     => 'nullable|numeric',
    //         'current_stock'     => 'nullable|numeric',
    //         'purchase_price'    => 'nullable|numeric',
    //         'selling_price'     => 'nullable|numeric',
    //         'status'            => 'required|in:active,inactive',
    //         'description'       => 'nullable|string',
    //         'gst_rate'          => 'required|numeric|min:0|max:28',
    //         'discount_type'     => 'required|in:percent,flat',
    //         'discount_value'    => 'required|numeric|min:0',
    //     ]);

    //     if (
    //         $request->discount_type === 'percent' &&
    //         $request->discount_value > 100
    //     ) {
    //         return back()->withErrors([
    //             'discount_value' => 'Percentage discount cannot exceed 100%'
    //         ]);
    //     }

    //     /** ---------------------------
    //      *  UPDATE MAIN ITEM
    //      *  ---------------------------
    //      */
    //     $inventoryItem->update([
    //         'name'            => $request->name,
    //         'sku'             => $request->sku,
    //         'unit_id'         => $request->unit_id,
    //         'brand_id'        => $request->brand_id,
    //         'opening_stock'   => $request->opening_stock ?? 0,
    //         'current_stock'   => $request->current_stock ?? 0,
    //         'purchase_price'  => $request->purchase_price,
    //         'selling_price'   => $request->selling_price,
    //         'description'     => $request->description,
    //         'status'          => $request->status,
    //         'gst_rate'        => $request->gst_rate,

    //         // ✅ DISCOUNT FIELDS
    //         'discount_type'  => $request->discount_type,
    //         'discount_value' => $request->discount_value,
    //     ]);

    //     /** ---------------------------
    //      *  SYNC CATEGORIES
    //      *  ---------------------------
    //      */
    //     $inventoryItem->categories()->sync($request->category_ids);

    //     /** ---------------------------
    //      *  SYNC SUB CATEGORIES
    //      *  ---------------------------
    //      */
    //     if ($request->filled('sub_category_ids')) {
    //         $inventoryItem->subCategories()->sync($request->sub_category_ids);
    //     } else {
    //         // Remove all sub categories if none selected
    //         $inventoryItem->subCategories()->detach();
    //     }

    //     return redirect()
    //         ->route('admin.inventory-items.index')
    //         ->with('success', 'Inventory item updated successfully');
    // }

    public function update(Request $request, InventoryItem $inventoryItem)
    {
        $validated = $request->validate([
            'name'               => 'required|string|max:255',
            'sku'                => 'required|string|max:100|unique:inventory_items,sku,' . $inventoryItem->id,
            'unit_id'            => 'required|exists:inventory_units,id',
            'brand_id'           => 'required|exists:inventory_brands,id',

            'vendor_id'          => 'nullable|integer|exists:inventory_vendors,id',

            'category_ids'       => 'required|array|min:1',
            'category_ids.*'     => 'exists:inventory_categories,id',

            'sub_category_ids'   => 'nullable|array',
            'sub_category_ids.*' => 'exists:inventory_categories,id',

            'stocks'             => 'nullable|numeric|min:0',
            'minimum_stock'      => 'nullable|numeric|min:0',

            'purchase_price'     => 'nullable|numeric|min:0',
            'selling_price'      => 'nullable|numeric|min:0',

            'status'             => 'required|in:active,inactive',
            'description'        => 'nullable|string',

            'gst_rate'           => 'required|numeric|min:0|max:28',

            'discount_type'      => 'required|in:percent,flat',
            'discount_value'     => 'required|numeric|min:0',
        ]);

        // ✅ Prevent % discount > 100
        if (
            $validated['discount_type'] === 'percent' &&
            $validated['discount_value'] > 100
        ) {
            return back()->withErrors([
                'discount_value' => 'Percentage discount cannot exceed 100%'
            ]);
        }

        // ✅ Check if ANY transactions exist (except opening)
        $hasTransactions = StockTransaction::where('inventory_item_id', $inventoryItem->id)
            ->where('reference_type', '!=', 'opening')
            ->exists();

        // ✅ Check if PURCHASE transactions exist
        $hasPurchaseTransactions = StockTransaction::where('inventory_item_id', $inventoryItem->id)
            ->where('reference_type', 'purchase')
            ->exists();

        // 🚨 Prevent vendor change after purchase exists
        if (
            $hasPurchaseTransactions &&
            $validated['vendor_id'] != $inventoryItem->vendor_id
        ) {
            return back()->withErrors([
                'vendor_id' => 'Vendor cannot be changed after purchase transactions exist.'
            ]);
        }

        DB::transaction(function () use ($validated, $inventoryItem, $hasTransactions, $request) {

            /** ==============================
             *  1️⃣ UPDATE MASTER DATA
             * ============================== */
            $inventoryItem->update([
                'name'           => $validated['name'],
                'sku'            => strtoupper($validated['sku']),
                'unit_id'        => $validated['unit_id'],
                'brand_id'       => $validated['brand_id'],
                'vendor_id'      => $validated['vendor_id'] ?? null,
                'purchase_price' => $validated['purchase_price'] ?? 0,
                'selling_price'  => $validated['selling_price'] ?? 0,
                'description'    => $validated['description'] ?? null,
                'minimum_stock'  => $validated['minimum_stock'] ?? 0,
                'status'         => $validated['status'],
                'gst_rate'       => $validated['gst_rate'],
                'discount_type'  => $validated['discount_type'],
                'discount_value' => $validated['discount_value'],
            ]);

            /** ==============================
             *  2️⃣ OPENING STOCK PROTECTION
             * ============================== */
            if (!$hasTransactions) {

                if (
                    $request->has('stocks') &&
                    $validated['stocks'] != $inventoryItem->stocks
                ) {

                    $stocks = $validated['stocks'];

                    $inventoryItem->update([
                        'stocks' => $stocks
                    ]);

                    // Delete old opening batch
                    InventoryBatch::where('inventory_item_id', $inventoryItem->id)
                        ->where('reference_type', 'opening')
                        ->delete();

                    // Delete old opening transaction
                    StockTransaction::where('inventory_item_id', $inventoryItem->id)
                        ->where('reference_type', 'opening')
                        ->delete();

                    if ($stocks > 0) {

                        InventoryBatch::create([
                            'inventory_item_id'  => $inventoryItem->id,
                            'quantity'           => $stocks,
                            'remaining_quantity' => $stocks,
                            'unit_cost'          => $inventoryItem->purchase_price,
                            'selling_price'      => $inventoryItem->selling_price,
                            'reference_type'     => 'opening',
                            'reference_id'       => $inventoryItem->id,
                        ]);

                        StockTransaction::create([
                            'inventory_item_id' => $inventoryItem->id,
                            'type'              => 'IN',
                            'quantity'          => $stocks,
                            'reference_type'    => 'opening',
                            'reference_id'      => $inventoryItem->id,
                            'note'              => 'Opening Stock Updated',
                        ]);
                    }
                }

            } else {

                if (
                    $request->has('stocks') &&
                    $validated['stocks'] != $inventoryItem->stocks
                ) {
                    throw new \Exception(
                        'Opening stock cannot be modified after transactions exist.'
                    );
                }
            }

            /** ==============================
             *  3️⃣ CATEGORY SYNC
             * ============================== */
            // $inventoryItem->categories()->sync($validated['category_ids']);

            // /** ==============================
            //  *  4️⃣ SUB CATEGORY SYNC
            //  * ============================== */
            // if (!empty($validated['sub_category_ids'])) {
            //     $inventoryItem->subCategories()->sync($validated['sub_category_ids']);
            // } else {
            //     $inventoryItem->subCategories()->detach();
            // }

            $inventoryItem->categories()->detach();

            /** Attach Main Categories */
            foreach ($validated['category_ids'] as $id) {
                $inventoryItem->categories()->attach($id, [
                    'type' => 'category'
                ]);
            }

            /** Attach Sub Categories */
            if (!empty($validated['sub_category_ids'])) {
                foreach ($validated['sub_category_ids'] as $id) {
                    $inventoryItem->categories()->attach($id, [
                        'type' => 'sub_category'
                    ]);
                }
            }
        });

        return redirect()
            ->route('admin.inventory-items.index')
            ->with('success', 'Inventory item updated successfully.');
    }




    /**
     * Store inventory item
     */
    // public function store(Request $request)
    // {
    //     $validated = $request->validate([
    //         'category_id'      => 'required|exists:inventory_categories,id',
    //         'sub_category_id'  => 'nullable|exists:inventory_categories,id',
    //         'unit_id'          => 'required|exists:units,id',
    //         'name'             => 'required|string|max:255',
    //         'sku'              => 'required|string|max:100|unique:inventory_items,sku',
    //         'description'      => 'nullable|string',
    //         'opening_stock'    => 'nullable|numeric|min:0',
    //         'current_stock'    => 'nullable|numeric|min:0',
    //         'purchase_price'   => 'nullable|numeric|min:0',
    //         'selling_price'   => 'nullable|numeric|min:0',
    //         'status'           => 'required|in:active,inactive',
    //     ]);

    //     DB::beginTransaction();

    //     try {

    //         $currentStock = $request->filled('current_stock')
    //             ? $request->current_stock
    //             : ($request->opening_stock ?? 0);

    //         InventoryItem::create([
    //             'category_id'     => $validated['category_id'],
    //             'sub_category_id' => $validated['sub_category_id'] ?? null,
    //             'unit_id'         => $validated['unit_id'],
    //             'name'            => $validated['name'],
    //             'sku'             => strtoupper($validated['sku']),
    //             'description'     => $validated['description'] ?? null,
    //             'opening_stock'   => $validated['opening_stock'] ?? 0,
    //             'current_stock'   => $currentStock,
    //             'purchase_price'  => $validated['purchase_price'] ?? 0,
    //             'selling_price'   => $validated['selling_price'] ?? 0,
    //             'status'          => $validated['status'],
    //         ]);

    //         DB::commit();

    //         return redirect()
    //             ->route('admin.inventory-items.index')
    //             ->with('success', 'Inventory item created successfully');

    //     } catch (\Exception $e) {

    //         DB::rollBack();

    //         return back()
    //             ->withInput()
    //             ->with('error', 'Something went wrong while saving the item');
    //     }
    // }

    /**
     * Display the specified resource.
     */
    public function show(InventoryItem $inventoryItem)
    {
        //
    }

    public function toggleStatus(InventoryItem $inventoryItem)
    {
        $inventoryItem->update([
            'status' => $inventoryItem->status === 'active' ? 'inactive' : 'active'
        ]);

        return back()->with('success', 'Status updated');
    }

    public function trash()
    {
        $items = InventoryItem::onlyTrashed()
            ->with(['unit','categories','subCategories'])
            ->latest()
            ->get();

        return view('admin.inventory-items.trash', compact('items'));
    }




    /**
     * Remove the specified resource from storage.
     */
    public function destroy(InventoryItem $inventoryItem)
    {
        DB::transaction(function () use ($inventoryItem) {
            $inventoryItem->delete(); // soft delete
        });

        return redirect()
            ->route('admin.inventory-items.index')
            ->with('success', 'Item moved to trash');
    }

    public function restore($id)
    {
        $item = InventoryItem::onlyTrashed()->findOrFail($id);

        DB::transaction(function () use ($item) {
            $item->restore();
        });

        return back()->with('success', 'Item restored successfully');
    }

    public function force($id)
    {
        $item = InventoryItem::onlyTrashed()->findOrFail($id);

        DB::transaction(function () use ($item) {

            // detach pivot relations first
            $item->categories()->detach();
            $item->subCategories()->detach();

            $item->forceDelete();
        });

        return back()->with('success', 'Item permanently deleted');
    }




    public function getSubCategories(Request $request)
    {
        $categoryIds = $request->category_ids ?? [];

        if (empty($categoryIds)) {
            return response()->json([]);
        }

        $subCategories = InventoryCategory::whereIn('parent_id', $categoryIds)
            ->where('status', 'active')
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        return response()->json($subCategories);
    }
}
