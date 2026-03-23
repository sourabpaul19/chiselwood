<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\ProjectStatus;
use App\Models\Lead;
use App\Models\LeadStatus;
use App\Models\Invoice;
use App\Models\Expense;

class AdminController extends Controller
{
    public function dashboard()
    {
        $totalProjects = Project::count();

        // Get total leads
        $totalLeads = Lead::count();

        // Get lead statuses and their counts
        $leadStatuses = LeadStatus::withCount('leads')->get();

        $projectStatuses = ProjectStatus::withCount('projects')->get();

        $activeStatusIds = ProjectStatus::where('status', 'active')->pluck('id');
        $inactiveStatusIds = ProjectStatus::where('status', 'inactive')->pluck('id');

        $activeProjects = Project::whereIn('project_status_id', $activeStatusIds)
            ->whereNotIn('project_status_id', $inactiveStatusIds) // optional if you have inactive
            ->count();

        $completedStatusId = ProjectStatus::where('name', 'Completed')->value('id');
        $completedProjects = Project::where('project_status_id', $completedStatusId)->count();

        $onHoldStatusId = ProjectStatus::where('name', 'On Hold')->value('id');
        $onHoldProjects = Project::where('project_status_id', $onHoldStatusId)->count();


        $totalSales = Invoice::where('is_final',1)->sum('grand_total');
        $totalExpenses = Expense::sum('amount');
        $totalProfit = $totalSales - $totalExpenses;
        $outstanding = Invoice::where('payment_status','!=','paid')->sum('grand_total');

        $stockValue = \DB::table('inventory_batches')
            ->selectRaw('SUM(remaining_quantity * unit_cost) as value')
            ->value('value') ?? 0;

        return view('admin.dashboard', compact(
            'totalProjects',
            'activeProjects',
            'completedProjects',
            'onHoldProjects',
            'totalLeads',
            'leadStatuses',
            'projectStatuses',
            'totalSales',
            'totalExpenses',
            'totalProfit',
            'outstanding',
            'stockValue',
        ));
    }
    // public function dashboard()
    // {
    //     $totalProjects = Project::count();
    //     $activeProjects = Project::whereIn('status', ['Planning', 'In Progress'])->count();
    //     $completedProjects = Project::where('status', 'Completed')->count();
    //     $onHoldProjects = Project::where('status', 'On Hold')->count();

    //     return view('admin.dashboard', compact(
    //         'totalProjects',
    //         'activeProjects',
    //         'completedProjects',
    //         'onHoldProjects'
    //     ));
    // }
}