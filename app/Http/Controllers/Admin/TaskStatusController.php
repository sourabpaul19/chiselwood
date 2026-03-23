<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TaskStatus;
use Illuminate\Http\Request;

class TaskStatusController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = TaskStatus::query();

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
            $status = TaskStatus::findOrFail($request->edit);
        }

        return view(
            'admin.task-statuses.index',
            compact('statuses', 'status')
        );
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:task_statuses,name',
            'status' => 'required'
        ]);

        TaskStatus::create($request->only('name', 'status'));

        return back()->with('success', 'Task status created');
    }

    public function edit(TaskStatus $taskStatus)
    {
        return redirect()
            ->route('admin.task-statuses.index', [
                'edit' => $taskStatus->id
            ]);
    }

    public function update(Request $request, TaskStatus $taskStatus)
    {
        $request->validate([
            'name'   => 'required|unique:task_statuses,name,' . $taskStatus->id,
            'status' => 'required|in:active,inactive',
        ]);

        $taskStatus->update($request->only('name', 'status'));

        return redirect()->route('admin.task-statuses.index')
            ->with('success', 'Task status updated');
    }

    public function toggleStatus($id)
    {
        $statuses = TaskStatus::findOrFail($id);

        $statuses->update([
            'status' => $statuses->status === 'active'
                ? 'inactive'
                : 'active'
        ]);

        return back()->with('success', 'Employee type status updated');
    }

    public function destroy(TaskStatus $taskStatus)
    {
        $taskStatus->delete();
        return back()->with('success', 'Moved to trash');
    }

    public function trash()
    {
        $statuses = TaskStatus::onlyTrashed()->get();
        return view('admin.task-statuses.trash', compact('statuses'));
    }

    public function restore($id)
    {
        TaskStatus::onlyTrashed()->findOrFail($id)->restore();
        return redirect()->route('admin.task-statuses.index')
            ->with('success', 'Restored successfully');
    }

    public function force($id)
    {
        TaskStatus::onlyTrashed()->findOrFail($id)->forceDelete();
        return back()->with('success', 'Deleted permanently');
    }
}
