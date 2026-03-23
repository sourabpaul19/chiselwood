<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LeadStatus;
use Illuminate\Http\Request;

class LeadStatusController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = LeadStatus::query();

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
            $status = LeadStatus::findOrFail($request->edit);
        }

        return view(
            'admin.lead-statuses.index',
            compact('statuses', 'status')
        );
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:lead_statuses,name',
            'status' => 'required'
        ]);

        LeadStatus::create($request->only('name', 'status'));

        return back()->with('success', 'Project status created');
    }

    public function edit(LeadStatus $leadStatus)
    {
        return redirect()
            ->route('admin.lead-statuses.index', [
                'edit' => $leadStatus->id
            ]);
    }

    public function update(Request $request, LeadStatus $leadStatus)
    {
        $request->validate([
            'name'   => 'required|unique:lead_statuses,name,' . $leadStatus->id,
            'status' => 'required|in:active,inactive',
        ]);

        $leadStatus->update($request->only('name', 'status'));

        return redirect()->route('admin.lead-statuses.index')
            ->with('success', 'Project status updated');
    }

    public function toggleStatus($id)
    {
        $statuses = LeadStatus::findOrFail($id);

        $statuses->update([
            'status' => $statuses->status === 'active'
                ? 'inactive'
                : 'active'
        ]);

        return back()->with('success', 'Employee type status updated');
    }

    public function destroy(LeadStatus $leadStatus)
    {
        $leadStatus->delete();
        return back()->with('success', 'Moved to trash');
    }

    public function trash()
    {
        $statuses = LeadStatus::onlyTrashed()->get();
        return view('admin.lead-statuses.trash', compact('statuses'));
    }

    public function restore($id)
    {
        LeadStatus::onlyTrashed()->findOrFail($id)->restore();
        return redirect()->route('admin.lead-statuses.index')
            ->with('success', 'Restored successfully');
    }

    public function force($id)
    {
        LeadStatus::onlyTrashed()->findOrFail($id)->forceDelete();
        return back()->with('success', 'Deleted permanently');
    }
}
