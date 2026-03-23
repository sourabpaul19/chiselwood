<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LeadSource;
use Illuminate\Http\Request;

class LeadSourceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = LeadSource::query();

        /* ======================
        FILTERS
        ====================== */
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $sources = $query
            ->latest()
            ->paginate(10)
            ->withQueryString();

        /* ======================
        EDIT MODE (same page)
        ====================== */
        $source = null;
        if ($request->filled('edit')) {
            $source = LeadSource::findOrFail($request->edit);
        }

        $counts = [
            'all'      => LeadSource::count(),
            'active'   => LeadSource::where('status', 'active')->count(),
            'inactive' => LeadSource::where('status', 'inactive')->count(),
            'trash'    => LeadSource::onlyTrashed()->count(),
        ];

        return view(
            'admin.lead-sources.index',
            compact('sources', 'source', 'counts')
        );
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:lead_sources,name',
            'status' => 'required'
        ]);

        LeadSource::create($request->only('name', 'status'));

        return back()->with('success', 'Project status created');
    }

    public function edit(LeadSource $leadSource)
    {
        return redirect()
            ->route('admin.lead-sources.index', [
                'edit' => $leadSource->id
            ]);
    }

    public function update(Request $request, LeadSource $leadSource)
    {
        $request->validate([
            'name'   => 'required|unique:lead_sources,name,' . $leadSource->id,
            'status' => 'required|in:active,inactive',
        ]);

        $leadSource->update($request->only('name', 'status'));

        return redirect()->route('admin.lead-sources.index')
            ->with('success', 'Project status updated');
    }

    public function toggleStatus($id)
    {
        $sources = LeadSource::findOrFail($id);

        $sources->update([
            'status' => $sources->status === 'active'
                ? 'inactive'
                : 'active'
        ]);

        return back()->with('success', 'Employee type status updated');
    }

    public function destroy(LeadSource $leadSource)
    {
        $leadSource->delete();
        return back()->with('success', 'Moved to trash');
    }

    public function trash()
    {
        $sources = LeadSource::onlyTrashed()->get();
        return view('admin.lead-sources.trash', compact('sources'));
    }

    public function restore($id)
    {
        LeadSource::onlyTrashed()->findOrFail($id)->restore();
        return redirect()->route('admin.lead-sources.index')
            ->with('success', 'Restored successfully');
    }

    public function force($id)
    {
        LeadSource::onlyTrashed()->findOrFail($id)->forceDelete();
        return back()->with('success', 'Deleted permanently');
    }
}
