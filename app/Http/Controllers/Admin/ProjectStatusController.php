<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProjectStatus;
use Illuminate\Http\Request;

class ProjectStatusController extends Controller
{
    public function index(Request $request)
    {
        $query = ProjectStatus::query();

        /* ======================
        FILTERS
        ====================== */
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $statuses = $query
            ->latest()
            ->paginate(10)
            ->withQueryString();

        /* ======================
        EDIT MODE (same page)
        ====================== */
        $status = null;
        if ($request->filled('edit')) {
            $status = ProjectStatus::findOrFail($request->edit);
        }

        return view('admin.project-statuses.index', compact('statuses', 'status'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:project_statuses,name',
            'status' => 'required'
        ]);

        ProjectStatus::create($request->only('name', 'status'));

        return back()->with('success', 'Project status created');
    }

    public function edit(ProjectStatus $projectStatus)
    {
        return redirect()
            ->route('admin.project-statuses.index', [
                'edit' => $projectStatus->id
            ]);
    }

    public function update(Request $request, ProjectStatus $projectStatus)
    {
        $request->validate([
            'name'   => 'required|unique:project_statuses,name,' . $projectStatus->id,
            'status' => 'required|in:active,inactive',
        ]);

        $projectStatus->update($request->only('name', 'status'));

        return redirect()->route('admin.project-statuses.index')
            ->with('success', 'Project status updated');
    }

    public function toggleStatus($id)
    {
        $statuses = ProjectStatus::findOrFail($id);

        $statuses->update([
            'status' => $statuses->status === 'active'
                ? 'inactive'
                : 'active'
        ]);

        return back()->with('success', 'Employee type status updated');
    }

    public function destroy(ProjectStatus $projectStatus)
    {
        $projectStatus->delete();
        return back()->with('success', 'Moved to trash');
    }

    public function trash()
    {
        $statuses = ProjectStatus::onlyTrashed()->get();
        return view('admin.project-statuses.trash', compact('statuses'));
    }

    public function restore($id)
    {
        ProjectStatus::onlyTrashed()->findOrFail($id)->restore();
        return redirect()->route('admin.project-statuses.index')
            ->with('success', 'Restored successfully');
    }

    public function force($id)
    {
        ProjectStatus::onlyTrashed()->findOrFail($id)->forceDelete();
        return back()->with('success', 'Deleted permanently');
    }

}

