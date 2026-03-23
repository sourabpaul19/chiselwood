<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function index(Request $request)
    {
        $query = Department::query();

        /* ======================
        FILTERS
        ====================== */
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $departments = $query
            ->latest()
            ->paginate(10)
            ->withQueryString();

        /* ======================
        EDIT MODE (same page)
        ====================== */
        $department = null;
        if ($request->filled('edit')) {
            $department = Department::findOrFail($request->edit);
        }

        return view(
            'admin.departments.index',
            compact('departments', 'department')
        );
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'   => 'required|unique:departments,name',
            'status' => 'required|in:active,inactive',
        ]);

        Department::create($request->only('name', 'status'));

        return redirect()
            ->route('admin.departments.index')
            ->with('success', 'Department added');
    }

    /* ======================
    EDIT → REDIRECT TO INDEX
    ====================== */
    public function edit(Department $department)
    {
        return redirect()
            ->route('admin.departments.index', [
                'edit' => $department->id
            ]);
    }

    public function update(Request $request, Department $department)
    {
        $request->validate([
            'name'   => 'required|unique:departments,name,' . $department->id,
            'status' => 'required|in:active,inactive',
        ]);

        $department->update($request->only('name', 'status'));

        return redirect()
            ->route('admin.departments.index')
            ->with('success', 'Department updated');
    }

    public function toggleStatus($id)
    {
        $department = Department::findOrFail($id);

        $department->update([
            'status' => $department->status === 'active'
                ? 'inactive'
                : 'active'
        ]);

        return back()->with('success', 'Department status updated');
    }

    /* ======================
    SOFT DELETE
    ====================== */
    public function destroy(Department $department)
    {
        $department->delete();

        return back()->with('success', 'Department moved to trash');
    }

    public function trash()
    {
        $departments = Department::onlyTrashed()
            ->latest()
            ->get();

        return view('admin.departments.trash', compact('departments'));
    }

    public function restore($id)
    {
        Department::onlyTrashed()
            ->findOrFail($id)
            ->restore();

        return back()->with('success', 'Department restored');
    }

    public function force($id)
    {
        Department::onlyTrashed()
            ->findOrFail($id)
            ->forceDelete();

        return back()->with('success', 'Department permanently deleted');
    }
}
