<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use Illuminate\Support\Facades\DB;

class ExpenseController extends Controller
{
    public function index()
    {
        $expenses = Expense::with('category')
            ->latest()
            ->paginate(15);

        return view('admin.expenses.index', compact('expenses'));
    }

    public function create()
    {
        $categories = ExpenseCategory::where('status', 1)->get();
        return view('admin.expenses.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'expense_category_id' => 'required|exists:expense_categories,id',
            'expense_date' => 'required|date',
            'amount' => 'required|numeric|min:0'
        ]);

        DB::transaction(function () use ($request) {

            Expense::create([
                'expense_number' => 'EXP-' . time(),
                'expense_category_id' => $request->expense_category_id,
                'expense_date' => $request->expense_date,
                'amount' => $request->amount,
                'payment_method' => $request->payment_method,
                'note' => $request->note,
                'is_approved' => 1,
                'created_by' => auth()->id()
            ]);

        });

        return redirect()->route('admin.expenses.index')
            ->with('success', 'Expense Created Successfully');
    }

    public function destroy(Expense $expense)
    {
        $expense->delete();

        return back()->with('success', 'Expense Deleted Successfully');
    }
}
