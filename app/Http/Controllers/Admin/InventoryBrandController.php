<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InventoryBrand;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class InventoryBrandController extends Controller
{
    //
    public function index(Request $request)
    {
        $query = InventoryBrand::query();

        /* ======================
        FILTERS
        ====================== */
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $inventoryBrands = $query
            ->latest()
            ->paginate(10)
            ->withQueryString();

        /* ======================
        EDIT MODE (same page)
        ====================== */
        $inventoryBrand = null;
        if ($request->filled('edit')) {
            $inventoryBrand = InventoryBrand::findOrFail($request->edit);
        }

        return view(
            'admin.inventory-brands.index',
            compact('inventoryBrands', 'inventoryBrand')
        );
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'   => 'required|unique:inventory_brands,name',
            'status' => 'required|in:active,inactive',
        ]);

        //InventoryBrand::create($request->only('name', 'status'));
        InventoryBrand::create([
            'name'      => trim($request->name),
            'slug'      => Str::slug($request->name),
            'status'    => $request->status,
        ]);

        return redirect()
            ->route('admin.inventory-brands.index')
            ->with('success', 'Brand added');
    }

    /* ======================
    EDIT → REDIRECT TO INDEX
    ====================== */
    public function edit(InventoryBrand $inventoryBrand)
    {
        return redirect()
            ->route('admin.inventory-brands.index', [
                'edit' => $inventoryBrand->id
            ]);
    }

    public function update(Request $request, InventoryBrand $inventoryBrand)
    {
        $request->validate([
            'name'   => 'required|unique:inventory_brands,name,' . $inventoryBrand->id,
            'status' => 'required|in:active,inactive',
        ]);

        //$inventoryBrand->update($request->only('name', 'short_name', 'status'));
        InventoryBrand::create([
            'name'      => trim($request->name),
            'slug'      => Str::slug($request->name),
            'status'    => $request->status,
        ]);

        return redirect()
            ->route('admin.inventory-brands.index')
            ->with('success', 'Brand updated');
    }

    public function toggleStatus($id)
    {
        $inventoryBrand = InventoryBrand::findOrFail($id);

        $inventoryBrand->update([
            'status' => $inventoryBrand->status === 'active'
                ? 'inactive'
                : 'active'
        ]);

        return back()->with('success', 'Brand status updated');
    }

    /* ======================
    SOFT DELETE
    ====================== */
    public function destroy(InventoryBrand $inventoryBrand)
    {
        $inventoryBrand->delete();

        return back()->with('success', 'Brand moved to trash');
    }

    public function trash()
    {
        $inventoryBrands = InventoryBrand::onlyTrashed()
            ->latest()
            ->get();

        return view('admin.inventory-brands.trash', compact('inventoryBrands'));
    }

    // public function restore($id)
    // {
    //     InventoryBrand::onlyTrashed()
    //         ->findOrFail($id)
    //         ->restore();

    //     return back()->with('success', 'Brand restored');
    // }

    public function restore($id)
    {
        $brand = InventoryBrand::withTrashed()->findOrFail($id);
        $brand->restore();

        return redirect()->route('admin.inventory-brands.index')
            ->with('success', 'Brand restored successfully');
    }


    public function force($id)
    {
        InventoryBrand::onlyTrashed()
            ->findOrFail($id)
            ->forceDelete();

        return back()->with('success', 'Brand permanently deleted');
    }
}
