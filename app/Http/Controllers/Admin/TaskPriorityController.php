<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TaskPriority;
use Illuminate\Http\Request;

class TaskPriorityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = TaskPriority::query();

        /* ======================
        FILTERS
        ====================== */
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $priorities = $query
            ->latest()
            ->paginate(10)
            ->withQueryString();

        /* ======================
        EDIT MODE (same page)
        ====================== */
        $priority = null;
        if ($request->filled('edit')) {
            $priority = TaskPriority::findOrFail($request->edit);
        }

        return view(
            'admin.task-priorities.index',
            compact('priorities', 'priority')
        );
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:task_priorities,name',
            'status' => 'required'
        ]);

        TaskPriority::create($request->only('name', 'status'));

        return back()->with('success', 'Task status created');
    }

    public function edit(TaskPriority $taskPriority)
    {
        return redirect()
            ->route('admin.task-priorities.index', [
                'edit' => $taskPriority->id
            ]);
    }

    public function update(Request $request, TaskPriority $taskPriority)
    {
        $request->validate([
            'name'   => 'required|unique:task_priorities,name,' . $taskPriority->id,
            'status' => 'required|in:active,inactive',
        ]);

        $taskPriority->update($request->only('name', 'status'));

        return redirect()->route('admin.task-priorities.index')
            ->with('success', 'Task status updated');
    }

    public function toggleStatus($id)
    {
        $priorities = TaskPriority::findOrFail($id);

        $priorities->update([
            'status' => $priorities->status === 'active'
                ? 'inactive'
                : 'active'
        ]);

        return back()->with('success', 'Task Priority status updated');
    }

    public function destroy(TaskPriority $taskPriority)
    {
        $taskPriority->delete();
        return back()->with('success', 'Moved to trash');
    }

    public function trash()
    {
        $priorities = TaskPriority::onlyTrashed()->get();
        return view('admin.task-priorities.trash', compact('priorities'));
    }

    public function restore($id)
    {
        TaskPriority::onlyTrashed()->findOrFail($id)->restore();
        return redirect()->route('admin.task-priorities.index')
            ->with('success', 'Restored successfully');
    }

    public function force($id)
    {
        TaskPriority::onlyTrashed()->findOrFail($id)->forceDelete();
        return back()->with('success', 'Deleted permanently');
    }
}
