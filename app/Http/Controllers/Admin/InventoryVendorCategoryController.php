<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InventoryVendorCategory;
use Illuminate\Http\Request;

class InventoryVendorCategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = InventoryVendorCategory::query();

        /* ======================
        FILTERS
        ====================== */
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $categories = $query
            ->latest()
            ->paginate(10)
            ->withQueryString();

        /* ======================
        EDIT MODE (same page)
        ====================== */
        $category = null;
        if ($request->filled('edit')) {
            $category = InventoryVendorCategory::findOrFail($request->edit);
        }

        return view(
            'admin.inventory-vendor-category.index',
            compact('categories', 'category')
        );
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'   => 'required|unique:inventory_vendor_categories,name',
            'status' => 'required'
        ]);

        InventoryVendorCategory::create(
            $request->only('name', 'status')
        );

        return back()->with('success', 'Inventory vendor category created');
    }

    public function edit(InventoryVendorCategory $inventoryVendorCategory)
    {
        return redirect()->route(
            'admin.inventory-vendor-category.index',
            ['edit' => $inventoryVendorCategory->id]
        );
    }

    public function update(Request $request, InventoryVendorCategory $inventoryVendorCategory)
    {
        $request->validate([
            'name'   => 'required|unique:inventory_vendor_categories,name,' . $inventoryVendorCategory->id,
            'status' => 'required|in:active,inactive',
        ]);

        $inventoryVendorCategory->update(
            $request->only('name', 'status')
        );

        return redirect()
            ->route('admin.inventory-vendor-category.index')
            ->with('success', 'Inventory vendor category updated');
    }

    public function toggleStatus($id)
    {
        $category = InventoryVendorCategory::findOrFail($id);

        $category->update([
            'status' => $category->status === 'active'
                ? 'inactive'
                : 'active'
        ]);

        return back()->with('success', 'Status updated');
    }

    public function destroy(InventoryVendorCategory $inventoryVendorCategory)
    {
        $inventoryVendorCategory->delete();
        return back()->with('success', 'Moved to trash');
    }

    public function trash()
    {
        $categories = InventoryVendorCategory::onlyTrashed()->get();

        return view(
            'admin.inventory-vendor-category.trash',
            compact('categories')
        );
    }

    public function restore($id)
    {
        InventoryVendorCategory::onlyTrashed()
            ->findOrFail($id)
            ->restore();

        return redirect()
            ->route('admin.inventory-vendor-category.index')
            ->with('success', 'Restored successfully');
    }

    public function force($id)
    {
        InventoryVendorCategory::onlyTrashed()
            ->findOrFail($id)
            ->forceDelete();

        return back()->with('success', 'Deleted permanently');
    }
}