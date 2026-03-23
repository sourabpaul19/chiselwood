<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Lead;
use App\Models\LeadSource;
use App\Models\LeadStatus;
use App\Models\ProjectType;
use App\Models\Staff;

class LeadController extends Controller
{
    /**
     * Display all leads
     */
    // public function index()
    // {
    //     $leads = Lead::with([
    //         'source',
    //         'statusName',
    //         'projectType',
    //         'staff'
    //     ])->latest()->get();

    //     return view('admin.leads.index', compact('leads'));
    // }

    public function index(Request $request)
    {
        $query = Lead::with([
            'type',
            'leadStatus',
            'leadSource',
            'staff.user'
        ]);

        if ($request->filled('lead_status_id')) {
            $query->where('lead_status_id', $request->lead_status_id);
        }

        if ($request->filled('lead_source_id')) {
            $query->where('lead_source_id', $request->lead_source_id);
        }

        if ($request->filled('project_type_id')) {
            $query->where('project_type_id', $request->project_type_id);
        }

        if ($request->filled('staff_id')) {
            $query->where('staff_id', $request->staff_id);
        }

        /* Inquiry Date */
        if ($request->filled('from_date') && $request->filled('to_date')) {
            $query->whereBetween('created_at', [
                $request->from_date . ' 00:00:00',
                $request->to_date . ' 23:59:59'
            ]);
        }

        /* Lead Search */
        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('lead_id', 'like', "%{$search}%")
                ->orWhere('name', 'like', "%{$search}%")
                ->orWhere('contact_details', 'like', "%{$search}%");
            });
        }

        $leads = $query->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.leads.index', [
            'leads'     => $leads,
            'types'        => ProjectType::where('status', 'active')->get(),
            'leadStatuses' => LeadStatus::where('status', 'active')->get(),
            'leadSources'  => LeadSource::where('status', 'active')->get(),
            'staffs'       => Staff::with('user')->where('status', 'active')->get(),
        ]);
    }


    /**
     * Show create form
     */
    public function create()
    {
        return view('admin.leads.create', [
            'sources'  => LeadSource::where('status',1)->get(),
            'statuses' => LeadStatus::where('status',1)->get(),
            'projects' => ProjectType::where('status',1)->get(),
            'staffs'   => Staff::where('status',1)->get(),
        ]);
    }

    /**
     * Store new lead
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'            => 'required|string|max:255',
            'contact_details' => 'required|string|max:255',
            'lead_source_id'  => 'required|exists:lead_sources,id',
            'project_type_id' => 'required|exists:project_types,id',
            'lead_status_id'  => 'required|exists:lead_statuses,id',
        ]);

        $lead = Lead::create([
            'name'               => $request->name,
            'contact_details'    => $request->contact_details,
            'lead_source_id'     => $request->lead_source_id,
            'inquiry_date'       => $request->inquiry_date,
            'budget_expectation' => $request->budget_expectation,
            'project_type_id'    => $request->project_type_id,
            'lead_status_id'     => $request->lead_status_id,
            'notes'              => $request->notes,
            'follow_up_date'     => $request->follow_up_date,
            'staff_id'           => $request->staff_id,
            'status'             => $request->status ?? 1,
        ]);

        $lead->update([
            'lead_id' => 'LEAD' . str_pad($lead->id, 5, '0', STR_PAD_LEFT),
        ]);

        return redirect()
            ->route('admin.leads.index')
            ->with('success','Lead added successfully');
    }

    /**
     * Show edit form
     */
    public function edit(Lead $lead)
    {
        return view('admin.leads.edit', [
            'lead'     => $lead,
            'sources'  => LeadSource::where('status',1)->get(),
            'statuses' => LeadStatus::where('status',1)->get(),
            'projects' => ProjectType::where('status',1)->get(),
            'staffs'   => Staff::where('status',1)->get(),
        ]);
    }

    /**
     * Update lead
     */
    public function update(Request $request, Lead $lead)
    {
        $request->validate([
            'name'            => 'required|string|max:255',
            'contact_details' => 'required|string|max:255',
            'lead_source_id'  => 'required|exists:lead_sources,id',
            'project_type_id' => 'required|exists:project_types,id',
            'lead_status_id'  => 'required|exists:lead_statuses,id',
        ]);

        $lead->update([
            'name'               => $request->name,
            'contact_details'    => $request->contact_details,
            'lead_source_id'     => $request->lead_source_id,
            'inquiry_date'       => $request->inquiry_date,
            'budget_expectation' => $request->budget_expectation,
            'project_type_id'    => $request->project_type_id,
            'lead_status_id'     => $request->lead_status_id,
            'notes'              => $request->notes,
            'follow_up_date'     => $request->follow_up_date,
            'staff_id'           => $request->staff_id,
            'status'             => $request->status,
        ]);

        return redirect()
            ->route('admin.leads.index')
            ->with('success','Lead updated successfully');
    }

    public function show(Lead $lead)
    {
        $lead->load([
            'type',
            'leadStatus',
            'leadSource',
            'staff.user'
        ]);

        return view('admin.leads.show', compact('lead'));
    }

    public function toggleStatus(Lead $lead)
    {
        $lead->update([
            'status'=>$lead->status === 'active' ? 'inactive' : 'active'
        ]);

        return back()->with('success','Status updated');
    }

    /**
     * Soft delete lead
     */
    public function destroy(Lead $lead)
    {
        $lead->delete();

        //return back()->with('success','Lead deleted successfully');
        return redirect()
            ->route('admin.leads.index')
            ->with('success', 'Lead moved to trash');
    }

    public function trash()
    {
        $leads = Lead::onlyTrashed()->latest()->get();
        return view('admin.leads.trash', compact('leads'));
    }

    public function restore($id)
    {
        Lead::onlyTrashed()->findOrFail($id)->restore();
        return back()->with('success', 'Lead restored');
    }

    public function force($id)
    {
        $lead = Lead::onlyTrashed()->findOrFail($id);

        if ($lead->design_file) {
            Storage::disk('public')->delete($lead->design_file);
        }

        $lead->staffs()->detach();
        $lead->forceDelete();

        return back()->with('success', 'Lead permanently deleted');
    }
}
