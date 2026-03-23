<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Client;
use App\Models\Staff;
use App\Models\ProjectType;
use App\Models\ProjectStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProjectController extends Controller
{
    /* =========================
       LIST PROJECTS
    ========================= */
    public function index(Request $request)
    {
        $query = Project::with([
            'client.user',
            'type',
            'projectStatus',
            'staffs.user'
        ]);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('client_id')) {
            $query->where('client_id', $request->client_id);
        }

        if ($request->filled('project_type_id')) {
            $query->where('project_type_id', $request->project_type_id);
        }

        if ($request->filled('project_status_id')) {
            $query->where('project_status_id', $request->project_status_id);
        }

        if ($request->filled('staff_id')) {
            $query->whereHas('staffs', function ($q) use ($request) {
                $q->where('staff.id', $request->staff_id);
            });
        }

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%");

                $q->orWhereHas('client.user', function ($uq) use ($search) {
                    $uq->where('name', 'like', "%{$search}%")
                       ->orWhere('email', 'like', "%{$search}%");
                });
            });
        }

        $projects = $query->latest()->paginate(10)->withQueryString();

        return view('admin.projects.index', [
            'projects' => $projects,
            'types'    => ProjectType::where('status', 'active')->get(),
            'statuses' => ProjectStatus::where('status', 'active')->get(),
            'staffs'   => Staff::with('user')->where('status', 'active')->get(),
            'clients'  => Client::with('user')->where('status', 'active')->get(),
        ]);
    }

    /* =========================
       CREATE FORM
    ========================= */
    public function create()
    {
        return view('admin.projects.create', [
            'clients'  => Client::with('user')->where('status', 'active')->get(),
            'staffs'   => Staff::with('user')->where('status', 'active')->get(),
            'types'    => ProjectType::where('status', 'active')->get(),
            'statuses' => ProjectStatus::where('status', 'active')->get(),
        ]);
    }

    /* =========================
       STORE PROJECT
    ========================= */
    public function store(Request $request)
    {
        $request->validate([
            'name'                  => 'required|string|max:255',
            'client_id'             => 'required|exists:clients,id',
            'project_type_id'       => 'required|exists:project_types,id',
            'project_status_id'     => 'required|exists:project_statuses,id',
            'start_date'            => 'nullable|date',
            'estimated_end_date'    => 'nullable|date|after_or_equal:start_date',
            'actual_end_date'       => 'nullable|date|after_or_equal:start_date',
            'estimated_budget'      => 'nullable|numeric|min:0',
            'actual_cost'           => 'nullable|numeric|min:0',
            'progress'              => 'nullable|integer|min:0|max:100',
            'design_file'           => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:4096',
            'staff_ids'             => 'nullable|array',
            'staff_ids.*'           => 'exists:staff,id',
        ]);

        DB::transaction(function () use ($request) {

            $designPath = null;

            if ($request->hasFile('design_file')) {
                $designPath = $request->file('design_file')
                    ->store('projects/designs', 'public');
            }

            $project = Project::create([
                'name'                => $request->name,
                'client_id'           => $request->client_id,
                'project_type_id'     => $request->project_type_id,
                'project_status_id'   => $request->project_status_id,
                'start_date'          => $request->start_date,
                'estimated_end_date'  => $request->estimated_end_date,
                'actual_end_date'     => $request->actual_end_date,
                'estimated_budget'    => $request->estimated_budget,
                'actual_cost'         => $request->actual_cost,
                'location'            => $request->location,
                'progress'            => $request->progress ?? 0,
                'notes'               => $request->notes,
                'design_file'         => $designPath,
                'status'              => $request->status ?? 'active',
            ]);

            $project->update([
                'project_id' => 'PRJ' . str_pad($project->id, 5, '0', STR_PAD_LEFT),
            ]);

            if ($request->filled('staff_ids')) {
                $project->staffs()->sync($request->staff_ids);
            }
        });

        return redirect()
            ->route('admin.projects.index')
            ->with('success', 'Project created successfully');
    }

    /* =========================
       EDIT PROJECT
    ========================= */
    public function edit(Project $project)
    {
        $projectStaffIds = $project->staffs()->pluck('staff.id')->toArray();

        return view('admin.projects.edit', [
            'project'         => $project,
            'clients'         => Client::with('user')->where('status', 'active')->get(),
            'staffs'          => Staff::with('user')->where('status', 'active')->get(),
            'types'           => ProjectType::where('status', 'active')->get(),
            'statuses'        => ProjectStatus::where('status', 'active')->get(),
            'projectStaffIds' => $projectStaffIds,
        ]);
    }

    /* =========================
       UPDATE PROJECT
    ========================= */
    public function update(Request $request, Project $project)
    {
        $request->validate([
            'name'               => 'required|string|max:255',
            'estimated_end_date' => 'nullable|date|after_or_equal:start_date',
            'actual_end_date'    => 'nullable|date|after_or_equal:start_date',
            'design_file'        => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:4096',
        ]);

        if ($request->hasFile('design_file')) {
            if ($project->design_file) {
                Storage::disk('public')->delete($project->design_file);
            }

            $project->design_file = $request->file('design_file')
                ->store('projects/designs', 'public');
        }

        $project->update(
            $request->except('staff_ids', 'design_file')
        );

        if ($request->filled('staff_ids')) {
            $project->staffs()->sync($request->staff_ids);
        }

        //return back()->with('success', 'Project updated successfully');
        return redirect()
            ->route('admin.projects.index')
            ->with('success', 'Project updated successfully');
    }

    public function toggleStatus(Project $project)
    {
        $project->update([
            'status'=>$project->status === 'active' ? 'inactive' : 'active'
        ]);

        return back()->with('success','Status updated');
    }

    public function show(Project $project)
    {
        $project->load([
            'client.user',
            'type',
            'projectStatus',
            'staffs.user'
        ]);

        return view('admin.projects.show', compact('project'));
    }

    /* =========================
       DELETE / TRASH
    ========================= */
    public function destroy(Project $project)
    {
        $project->delete();
        //return back()->with('success', 'Project moved to trash');
        return redirect()
            ->route('admin.projects.index')
            ->with('success', 'Staff moved to trash');
    }

    public function trash()
    {
        $projects = Project::onlyTrashed()->latest()->get();
        return view('admin.projects.trash', compact('projects'));
    }

    public function restore($id)
    {
        Project::onlyTrashed()->findOrFail($id)->restore();
        return back()->with('success', 'Project restored');
    }

    public function force($id)
    {
        $project = Project::onlyTrashed()->findOrFail($id);

        if ($project->design_file) {
            Storage::disk('public')->delete($project->design_file);
        }

        $project->staffs()->detach();
        $project->forceDelete();

        return back()->with('success', 'Project permanently deleted');
    }
}
