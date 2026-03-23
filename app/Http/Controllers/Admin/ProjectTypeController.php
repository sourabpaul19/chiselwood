<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProjectType;
use Illuminate\Http\Request;

class ProjectTypeController extends Controller
{
    public function index(Request $request)
    {
        $query = ProjectType::query();

        /* ======================
        FILTERS
        ====================== */
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $types = $query
            ->latest()
            ->paginate(10)
            ->withQueryString();

        /* ======================
        EDIT MODE (same page)
        ====================== */
        $type = null;
        if ($request->filled('edit')) {
            $type = ProjectType::findOrFail($request->edit);
        }

        return view(
            'admin.project-types.index',
            compact('types', 'type')
        );

        // $types = ProjectType::latest()->get();
        // return view('admin.project-types.index', compact('types'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:project_types,name',
            'status' => 'required'
        ]);

        ProjectType::create($request->only('name', 'status'));

        return back()->with('success', 'Project type created');
    }

    public function edit(ProjectType $projectType)
    {
        return redirect()
            ->route('admin.project-types.index', [
                'edit' => $projectType->id
            ]);
    }

    public function update(Request $request, ProjectType $projectType)
    {
        $request->validate([
            'name'   => 'required|unique:project_types,name,' . $projectType->id,
            'status' => 'required|in:active,inactive',
        ]);

        $projectType->update($request->only('name', 'status'));

        return redirect()->route('admin.project-types.index')
            ->with('success', 'Project types updated');
    }

    public function toggleStatus($id)
    {
        $types = ProjectType::findOrFail($id);

        $types->update([
            'status' => $types->status === 'active'
                ? 'inactive'
                : 'active'
        ]);

        return back()->with('success', 'Employee type status updated');
    }

    public function destroy(ProjectType $projectType)
    {
        $projectType->delete();
        return back()->with('success', 'Moved to trash');
    }

    /* ======================
       TRASH
    ====================== */
    public function trash()
    {
        $types = ProjectType::onlyTrashed()->get();
        return view('admin.project-types.trash', compact('types'));
    }

    /* ======================
       RESTORE
    ====================== */
    public function restore($id)
    {
        ProjectType::onlyTrashed()->findOrFail($id)->restore();
        return redirect()->route('admin.project-types.index')
            ->with('success', 'Restored successfully');
    }

    /* ======================
       FORCE DELETE
    ====================== */
    public function force($id)
    {
        ProjectType::onlyTrashed()->findOrFail($id)->forceDelete();
        return back()->with('success', 'Deleted permanently');
    }
}
