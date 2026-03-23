<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InventoryUnit;
use Illuminate\Http\Request;

class InventoryUnitController extends Controller
{
    public function index(Request $request)
    {
        $query = InventoryUnit::query();

        /* ======================
        FILTERS
        ====================== */
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $inventoryUnits = $query
            ->latest()
            ->paginate(10)
            ->withQueryString();

        /* ======================
        EDIT MODE (same page)
        ====================== */
        $inventoryUnit = null;
        if ($request->filled('edit')) {
            $inventoryUnit = InventoryUnit::findOrFail($request->edit);
        }

        return view(
            'admin.inventory-units.index',
            compact('inventoryUnits', 'inventoryUnit')
        );
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'   => 'required|unique:inventory_units,name',
            'short_name'   => 'required|unique:inventory_units,short_name',
            'status' => 'required|in:active,inactive',
        ]);

        InventoryUnit::create($request->only('name', 'short_name', 'status'));

        return redirect()
            ->route('admin.inventory-units.index')
            ->with('success', 'Unit added');
    }

    /* ======================
    EDIT → REDIRECT TO INDEX
    ====================== */
    public function edit(InventoryUnit $inventoryUnit)
    {
        return redirect()
            ->route('admin.inventory-units.index', [
                'edit' => $inventoryUnit->id
            ]);
    }

    public function update(Request $request, InventoryUnit $inventoryUnit)
    {
        $request->validate([
            'name'   => 'required|unique:inventory_units,name,' . $inventoryUnit->id,
            'short_name'   => 'required|unique:inventory_units,short_name' . $inventoryUnit->id,
            'status' => 'required|in:active,inactive',
        ]);

        $inventoryUnit->update($request->only('name', 'short_name', 'status'));

        return redirect()
            ->route('admin.inventory-units.index')
            ->with('success', 'Unit updated');
    }

    public function toggleStatus($id)
    {
        $inventoryUnit = InventoryUnit::findOrFail($id);

        $inventoryUnit->update([
            'status' => $inventoryUnit->status === 'active'
                ? 'inactive'
                : 'active'
        ]);

        return back()->with('success', 'Unit status updated');
    }

    /* ======================
    SOFT DELETE
    ====================== */
    public function destroy(InventoryUnit $inventoryUnit)
    {
        $inventoryUnit->delete();

        return back()->with('success', 'Unit moved to trash');
    }

    public function trash()
    {
        $inventoryUnits = InventoryUnit::onlyTrashed()
            ->latest()
            ->get();

        return view('admin.inventory-units.trash', compact('inventoryUnits'));
    }

    // public function restore($id)
    // {
    //     InventoryUnit::onlyTrashed()
    //         ->findOrFail($id)
    //         ->restore();

    //     return back()->with('success', 'Unit restored');
    // }

    public function restore($id)
    {
        $unit = InventoryUnit::withTrashed()->findOrFail($id);
        $unit->restore();

        return redirect()->route('admin.inventory-units.index')
            ->with('success', 'Unit restored successfully');
    }


    public function force($id)
    {
        InventoryUnit::onlyTrashed()
            ->findOrFail($id)
            ->forceDelete();

        return back()->with('success', 'Unit permanently deleted');
    }
}
