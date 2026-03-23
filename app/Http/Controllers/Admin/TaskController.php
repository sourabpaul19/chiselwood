<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\TaskPriority;
use App\Models\TaskStatus;
use App\Models\User;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TaskController extends Controller
{
    /**
     * TASK LIST
     */
    public function index(Request $request)
    {
        $query = Task::with([
            'assignees',
            'project',
            'assignedStaff',
            'priority',
            'statusInfo',
        ]);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        /* Filter: Project */
        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        /* Filter: Status */
        if ($request->filled('status_id')) {
            $query->where('status_id', $request->status_id);
        }

        /* Filter: Assigned Staff */
        if ($request->filled('assigned_to')) {
            $query->whereHas('assignees', function ($q) use ($request) {
                $q->where('users.id', $request->assigned_to);
            });
        }

        /* Search */
        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                ->orWhere('task_id', 'like', "%{$search}%");

                $q->orWhereHas('project', function ($pq) use ($search) {
                    $pq->where('name', 'like', "%{$search}%");
                });

                $q->orWhereHas('assignedStaff', function ($sq) use ($search) {
                    $sq->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
                });
            });
        }

        $tasks = $query
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.tasks.index', [
            'tasks'    => $tasks,
            'projects' => Project::all(),
            'staffs'   => User::where('role', 'staff')
                                ->where('status', 'active')
                                ->get(),
            'statuses' => TaskStatus::active()->get(),
        ]);
    }

    /**
     * CREATE FORM
     */
    public function create()
    {
        return view('admin.tasks.create', [
            'projects'   => Project::all(),
            'staffs'   => User::where('role', 'staff')
                                ->where('status', 'active')
                                ->get(),
            'priorities' => TaskPriority::active()->get(),
            'statuses'   => TaskStatus::active()->get(),
        ]);
    }

    /**
     * STORE TASK
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'title'        => 'required|string|max:255',
            'project_id'   => 'nullable|exists:projects,id',
            'assigned_to'  => 'nullable|array',
            'assigned_to.*'=> 'exists:users,id',
            'priority_id'  => 'nullable|exists:task_priorities,id',
            'status_id'    => 'nullable|exists:task_statuses,id',
            'start_date'   => 'nullable|date',
            'due_date'     => 'nullable|date|after_or_equal:start_date',
            'actual_due_date' => 'nullable|date|after_or_equal:start_date',
            'description'  => 'nullable|string',
            'documents.*'  => 'nullable|file|max:10240',
        ]);

        /* Upload documents */
        if ($request->hasFile('documents')) {
            $files = [];
            foreach ($request->file('documents') as $file) {
                $name = time().'_'.$file->getClientOriginalName();
                $file->move(public_path('uploads/tasks'), $name);
                $files[] = 'uploads/tasks/'.$name;
            }
            $data['documents'] = $files;
        }

        // Remove assigned_to before create
        $assignees = $data['assigned_to'] ?? [];
        unset($data['assigned_to']);

        $task = Task::create($data);

        // Attach multiple users
        $task->assignees()->sync($assignees);

        return redirect()
            ->route('admin.tasks.index')
            ->with('success', 'Task created successfully');
    }

    /**
     * EDIT FORM
     */
    public function edit(Task $task)
    {
        return view('admin.tasks.edit', [
            'task'       => $task,
            'projects'   => Project::all(),
            'staffs'   => User::where('role', 'staff')
                                ->where('status', 'active')
                                ->get(),
            'priorities' => TaskPriority::active()->get(),
            'statuses'   => TaskStatus::active()->get(),
        ]);
    }

    /**
     * UPDATE TASK
     */
    public function update(Request $request, Task $task)
    {
        $data = $request->validate([
            'title'        => 'required|string|max:255',
            'project_id'   => 'nullable|exists:projects,id',
            'assigned_to'  => 'nullable|array',
            'assigned_to.*'=> 'exists:users,id',
            'priority_id'  => 'nullable|exists:task_priorities,id',
            'status_id'    => 'nullable|exists:task_statuses,id',
            'start_date'   => 'nullable|date',
            'due_date'     => 'nullable|date|after_or_equal:start_date',
            'actual_due_date' => 'nullable|date|after_or_equal:start_date',
            'description'  => 'nullable|string',
            'documents.*'  => 'nullable|file|max:10240',
        ]);

        /* Upload new documents */
        if ($request->hasFile('documents')) {
            $files = $task->documents ?? [];
            foreach ($request->file('documents') as $file) {
                $name = time().'_'.$file->getClientOriginalName();
                $file->move(public_path('uploads/tasks'), $name);
                $files[] = 'uploads/tasks/'.$name;
            }
            $data['documents'] = $files;
        }

        $assignees = $data['assigned_to'] ?? [];
        unset($data['assigned_to']);

        $task->update($data);

        // Sync assignees
        $task->assignees()->sync($assignees);

        return redirect()
            ->route('admin.tasks.index')
            ->with('success', 'Task updated successfully');
    }

    /**
     * DELETE (SOFT)
     */
    public function destroy(Task $task)
    {
        $task->delete();

        return back()->with('success', 'Task deleted successfully');
    }

    /**
     * KANBAN VIEW
     */
    public function kanban()
    {
        // Kanban columns (progress statuses)
        $statuses = TaskStatus::where('status', 'active')->get();

        // Only ACTIVE tasks
        $tasks = Task::with(['assignedStaff', 'priority'])
            ->where('status', 'active') // active/inactive
            ->get()
            ->groupBy('status_id');     // progress status

        return view('admin.tasks.kanban', compact('statuses', 'tasks'));
    }

    /**
     * AJAX DRAG & DROP
     */
    // public function updateStatus(Request $request, Task $task)
    // {
    //     $request->validate([
    //         'status_id' => 'required|exists:task_statuses,id'
    //     ]);

    //     // Get selected status
    //     $status = TaskStatus::findOrFail($request->status_id);

    //     // Update progress status
    //     $task->status_id = $status->id;

    //     // ✅ Set actual due date ONLY when status is Completed (one-time)
    //     if (
    //         strtolower($status->name) === 'completed' &&
    //         is_null($task->actual_due_date)
    //     ) {
    //         $task->actual_due_date = now()->toDateString();
    //     }

    //     $task->save();

    //     return response()->json(['success' => true]);
    // }

    // public function updateStatus(Request $request, Task $task)
    // {
    //     $request->validate([
    //         'status_id' => 'required|exists:task_statuses,id'
    //     ]);

    //     $status = TaskStatus::findOrFail($request->status_id);
    //     $previousStatus = $task->status_id; // optional, if you need previous status
    //     $task->status_id = $status->id;

    //     // If status is Completed, set actual_due_date automatically only if empty
    //     if (strtolower($status->name) === 'completed') {
    //         $task->actual_due_date = $task->actual_due_date ?? now()->toDateString();
    //     } else {
    //         // If status is changed to anything other than Completed, clear actual_due_date
    //         $task->actual_due_date = null;
    //     }

    //     $task->save();

    //     return response()->json(['success' => true]);
    // }

    // public function updateStatus(Request $request, Task $task)
    // {
    //     $request->validate([
    //         'status_id' => 'required|exists:task_statuses,id'
    //     ]);

    //     $status = TaskStatus::findOrFail($request->status_id);

    //     $task->status_id = $status->id;

    //     // ✅ ACTUAL DUE DATE LOGIC
    //     if (strtolower($status->name) === 'completed') {
    //         // set once when completed
    //         $task->actual_due_date = now()->toDateString();
    //     } else {
    //         // remove actual date if moved back
    //         $task->actual_due_date = null;
    //     }

    //     $task->save();

    //     return response()->json(['success' => true]);
    // }

    public function updateStatus(Request $request, Task $task)
    {
        $request->validate([
            'status_id' => 'required|exists:task_statuses,id',
        ]);

        $newStatus = TaskStatus::find($request->status_id);

        $task->status_id = $newStatus->id;

        // Logic for Completed
        if ($newStatus->name === 'Completed') {
            if (!$task->actual_due_date) {
                $task->actual_due_date = now()->toDateString();
            }
        } else {
            if ($task->actual_due_date) {
                $task->actual_due_date = null;
            }
        }

        // Logic for Overdue
        if (!in_array($newStatus->name, ['Completed', 'Cancelled']) && $task->due_date && now()->gt($task->due_date)) {
            $overdueStatus = TaskStatus::where('name', 'Overdue')->first();
            if ($overdueStatus) {
                $task->status_id = $overdueStatus->id;
            }
        }

        $task->save();

        return response()->json(['success' => true, 'task' => $task]);
    }

    /**
     * ACTIVE / INACTIVE TOGGLE
     */
    public function toggleStatus(Task $task)
    {
        $task->update([
            'status'=>$task->status === 'active' ? 'inactive' : 'active'
        ]);

        return back()->with('success','Status updated');
    }

    public function trash()
    {
        $tasks = Task::onlyTrashed()->latest()->get();
        return view('admin.tasks.trash', compact('tasks'));
    }

    public function restore($id)
    {
        Task::onlyTrashed()->findOrFail($id)->restore();
        return back()->with('success', 'Task restored');
    }

    public function force($id)
    {
        $task = Task::withTrashed()->findOrFail($id);

        if (!$task->trashed()) {
            return back()->with('info', 'Task is not deleted');
        }

        // Delete all associated documents
        if ($task->documents && is_array($task->documents)) {
            foreach ($task->documents as $document) {
                if (Storage::disk('public')->exists($document)) {
                    Storage::disk('public')->delete($document);
                }
            }
        }

        // Clear assigned staff (optional)
        $task->assigned_to = null;
        $task->save();

        // Permanently delete the task
        $task->forceDelete();

        return back()->with('success', 'Task and its documents permanently deleted');
    }

    public function calendar()
    {
        $tasks = Task::select(
                'id',
                'title',
                'start_date',
                'due_date',
                'status_id'
            )
            ->whereNotNull('start_date')
            ->get();

        return view('admin.tasks.calendar', compact('tasks'));
    }



}
