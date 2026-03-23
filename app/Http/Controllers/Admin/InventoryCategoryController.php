<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InventoryCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class InventoryCategoryController extends Controller
{
    public function index(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | FLAT LIST (for table, filters, search)
        |--------------------------------------------------------------------------
        */
        $listQuery = InventoryCategory::query();

        if ($request->filled('status')) {
            if ($request->status === 'trash') {
                $listQuery->onlyTrashed();
            } else {
                $listQuery->where('status', $request->status);
            }
        }

        if ($request->filled('search')) {
            $listQuery->where('name', 'like', '%' . $request->search . '%');
        }

        $categoryList = $listQuery
            ->orderBy('name')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | TREE (only for parent dropdown)
        |--------------------------------------------------------------------------
        */
        // $categoryTree = InventoryCategory::whereNull('parent_id')
        //     ->with('childrenRecursive')
        //     ->orderBy('name')
        //     ->get();

        $categoryTree = InventoryCategory::whereNull('parent_id')
    ->where('status', 'active') // ✅ only active parents
    ->with('childrenRecursive')
    ->orderBy('name')
    ->get();

        /*
        |--------------------------------------------------------------------------
        | Edit Mode
        |--------------------------------------------------------------------------
        */
        $category = null;
        if ($request->filled('edit')) {
            $category = InventoryCategory::withTrashed()->findOrFail($request->edit);
        }

        /*
        |--------------------------------------------------------------------------
        | Counts (WP style)
        |--------------------------------------------------------------------------
        */
        $counts = [
            'all'      => InventoryCategory::count(),
            'active'   => InventoryCategory::where('status', 'active')->count(),
            'inactive' => InventoryCategory::where('status', 'inactive')->count(),
            'trash'    => InventoryCategory::onlyTrashed()->count(),
        ];

        return view('admin.inventory-categories.index', compact(
            'categoryTree',
            'categoryList',
            'category',
            'counts'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'      => 'required|string|max:255|unique:inventory_categories,name',
            'parent_id' => 'nullable|exists:inventory_categories,id',
            'status'    => 'required|in:active,inactive',
        ]);

        InventoryCategory::create([
            'name'      => trim($request->name),
            'slug'      => Str::slug($request->name),
            'parent_id' => $request->parent_id,
            'status'    => $request->status,
        ]);

        return back()->with('success', 'Inventory category created successfully');
    }


    public function edit(InventoryCategory $inventoryCategory)
    {
        return redirect()->route(
            'inventory-categories.index',
            ['edit' => $inventoryCategory->id]
        );
    }

    public function update(Request $request, InventoryCategory $inventoryCategory)
    {
        $request->validate([
            'name'      => 'required|unique:inventory_categories,name,' . $inventoryCategory->id,
            'parent_id' => 'nullable|exists:inventory_categories,id',
            'status'    => 'required|in:active,inactive',
        ]);

        $inventoryCategory->update([
            'name'      => $request->name,
            'slug'      => Str::slug($request->name),
            'parent_id' => $request->parent_id,
            'status'    => $request->status,
        ]);

        return redirect()->route('admin.inventory-categories.index')
            ->with('success', 'Inventory category updated');
    }

    public function toggleStatus($id)
    {
        $category = InventoryCategory::findOrFail($id);

        $category->update([
            'status' => $category->status === 'active'
                ? 'inactive'
                : 'active'
        ]);

        return back()->with('success', 'Category status updated');
    }

    public function destroy(InventoryCategory $inventoryCategory)
    {
        $inventoryCategory->delete();
        return back()->with('success', 'Moved to trash');
    }

    public function trash()
    {
        $categories = InventoryCategory::onlyTrashed()
            ->whereNull('parent_id')
            ->with('children')
            ->get();

        return view(
            'admin.inventory-categories.trash',
            compact('categories')
        );
    }

    // public function restore($id)
    // {
    //     InventoryCategory::onlyTrashed()
    //         ->findOrFail($id)
    //         ->restore();

    //     return redirect()->route('admin.inventory-categories.index')
    //         ->with('success', 'Category restored successfully');
    // }

    public function restore($id)
    {
        $category = InventoryCategory::onlyTrashed()->findOrFail($id);
        $category->restore();

        return back()->with('success', 'Category restored successfully');
    }


    public function force($id)
    {
        $category = InventoryCategory::withTrashed()->findOrFail($id);

        $category->forceDelete();

        return redirect()
            ->route('admin.inventory-categories.index')
            ->with('success', 'Category permanently deleted');
    }

    public function children(InventoryCategory $category)
    {
        return response()->json(
            InventoryCategory::where('parent_id', $category->id)
                ->where('status', 'active')
                ->select('id', 'name')
                ->orderBy('name')
                ->get()
        );
    }

}