<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VendorCategory;
use Illuminate\Http\Request;

class VendorCategoryController extends Controller
{
    //
    public function index(Request $request)
    {
        $query = VendorCategory::query();

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
            $category = VendorCategory::findOrFail($request->edit);
        }

        return view(
            'admin.vendor-category.index',
            compact('categories', 'category')
        );
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:vendor_categories,name',
            'status' => 'required'
        ]);

        VendorCategory::create($request->only('name', 'status'));

        return back()->with('success', 'Project status created');
    }

    public function edit(VendorCategory $vendorCategory)
    {
        return redirect()
            ->route('admin.vendor-category.index', [
                'edit' => $vendorCategory->id
            ]);
    }

    public function update(Request $request, VendorCategory $vendorCategory)
    {
        $request->validate([
            'name'   => 'required|unique:vendor_categories,name,' . $vendorCategory->id,
            'status' => 'required|in:active,inactive',
        ]);

        $vendorCategory->update($request->only('name', 'status'));

        return redirect()->route('admin.vendor-category.index')
            ->with('success', 'Project status updated');
    }

    public function toggleStatus($id)
    {
        $categories = VendorCategory::findOrFail($id);

        $categories->update([
            'status' => $categories->status === 'active'
                ? 'inactive'
                : 'active'
        ]);

        return back()->with('success', 'Employee type status updated');
    }

    public function destroy(VendorCategory $vendorCategory)
    {
        $vendorCategory->delete();
        return back()->with('success', 'Moved to trash');
    }

    public function trash()
    {
        $categories = VendorCategory::onlyTrashed()->get();
        return view('admin.vendor-category.trash', compact('categories'));
    }

    public function restore($id)
    {
        VendorCategory::onlyTrashed()->findOrFail($id)->restore();
        return redirect()->route('admin.vendor-category.index')
            ->with('success', 'Restored successfully');
    }

    public function force($id)
    {
        VendorCategory::onlyTrashed()->findOrFail($id)->forceDelete();
        return back()->with('success', 'Deleted permanently');
    }
}
