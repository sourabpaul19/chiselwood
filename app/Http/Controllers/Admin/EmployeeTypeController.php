<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmployeeType;
use Illuminate\Http\Request;

class EmployeeTypeController extends Controller
{
    public function index(Request $request)
    {
        $query = EmployeeType::query();

        /* ======================
        FILTERS
        ====================== */
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $employeeTypes = $query
            ->latest()
            ->paginate(10)
            ->withQueryString();

        /* ======================
        EDIT MODE (same page)
        ====================== */
        $employeeType = null;
        if ($request->filled('edit')) {
            $employeeType = EmployeeType::findOrFail($request->edit);
        }

        return view(
            'admin.employee-types.index',
            compact('employeeTypes', 'employeeType')
        );
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'   => 'required|unique:employee_types,name',
            'status' => 'required|in:active,inactive',
        ]);

        EmployeeType::create($request->only('name', 'status'));

        return redirect()
            ->route('admin.employee-types.index')
            ->with('success', 'Employee type added');
    }

    /* ======================
    EDIT → REDIRECT TO INDEX
    ====================== */
    public function edit(EmployeeType $employeeType)
    {
        return redirect()
            ->route('admin.employee-types.index', [
                'edit' => $employeeType->id
            ]);
    }

    public function update(Request $request, EmployeeType $employeeType)
    {
        $request->validate([
            'name'   => 'required|unique:employee_types,name,' . $employeeType->id,
            'status' => 'required|in:active,inactive',
        ]);

        $employeeType->update($request->only('name', 'status'));

        return redirect()
            ->route('admin.employee-types.index')
            ->with('success', 'Employee type updated');
    }

    public function toggleStatus($id)
    {
        $employeeType = EmployeeType::findOrFail($id);

        $employeeType->update([
            'status' => $employeeType->status === 'active'
                ? 'inactive'
                : 'active'
        ]);

        return back()->with('success', 'Employee type status updated');
    }

    /* ======================
    SOFT DELETE
    ====================== */
    public function destroy(EmployeeType $employeeType)
    {
        $employeeType->delete();

        return back()->with('success', 'Employee type moved to trash');
    }

    public function trash()
    {
        $employeeTypes = EmployeeType::onlyTrashed()
            ->latest()
            ->get();

        return view('admin.employee-types.trash', compact('employeeTypes'));
    }

    public function restore($id)
    {
        EmployeeType::onlyTrashed()
            ->findOrFail($id)
            ->restore();

        return back()->with('success', 'Employee type restored');
    }

    public function force($id)
    {
        EmployeeType::onlyTrashed()
            ->findOrFail($id)
            ->forceDelete();

        return back()->with('success', 'Employee type permanently deleted');
    }
}
