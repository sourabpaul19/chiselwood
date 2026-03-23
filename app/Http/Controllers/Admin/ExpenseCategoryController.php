<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ExpenseCategory;

class ExpenseCategoryController extends Controller
{

    public function index(Request $request)
    {
        $query = ExpenseCategory::query();

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
            $category = ExpenseCategory::findOrFail($request->edit);
        }

        return view(
            'admin.expense-categories.index',
            compact('categories', 'category')
        );

        // $types = ProjectType::latest()->get();
        // return view('admin.expense-categories.index', compact('types'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:expense_categories,name',
            'status' => 'required'
        ]);

        ExpenseCategory::create($request->only('name', 'status'));

        return back()->with('success', 'Expense Category created');
    }

    public function edit(ExpenseCategory $expense_category)
    {
        return redirect()
            ->route('admin.expense-categories.index', [
                'edit' => $expense_category->id
            ]);
    }

    public function update(Request $request, ExpenseCategory $expense_category)
    {
        $request->validate([
            'name'   => 'required|unique:expense_categories,name,' . $expense_category->id,
            'status' => 'required|in:active,inactive',
        ]);

        $expense_category->update($request->only('name', 'status'));

        return redirect()->route('admin.expense-categories.index')
            ->with('success', 'Expense Categories updated');
    }

    public function toggleStatus($id)
    {
        $categories = ExpenseCategory::findOrFail($id);

        $categories->update([
            'status' => $categories->status === 'active'
                ? 'inactive'
                : 'active'
        ]);

        return back()->with('success', 'Employee type status updated');
    }

    public function destroy(ExpenseCategory $expense_category)
    {
        $expense_category->delete();
        return back()->with('success', 'Moved to trash');
    }

    /* ======================
       TRASH
    ====================== */
    public function trash()
    {
        $categories = ExpenseCategory::onlyTrashed()->get();
        return view('admin.expense-categories.trash', compact('categories'));
    }

    /* ======================
       RESTORE
    ====================== */
    public function restore($id)
    {
        ExpenseCategory::onlyTrashed()->findOrFail($id)->restore();
        return redirect()->route('admin.expense-categories.index')
            ->with('success', 'Restored successfully');
    }

    /* ======================
       FORCE DELETE
    ====================== */
    public function force($id)
    {
        ExpenseCategory::onlyTrashed()->findOrFail($id)->forceDelete();
        return back()->with('success', 'Deleted permanently');
    }
}
