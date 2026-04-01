<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\Vendor;
use Illuminate\Support\Facades\Auth;

class VendorDashboardController extends Controller
{
    /**
     * Vendor Project List
     */
    public function dashboard()
    {
        $user = auth()->user();

        // Example based on your system

        return view('vendor.dashboard', compact('user'));
    }
    public function projects()
    {
        $userId = Auth::id();

        // Get vendor record using logged-in user
        $vendor = Vendor::where('user_id', $userId)->first();

        if (!$vendor) {
            abort(404, 'Vendor not found');
        }

        // Fetch projects assigned to this vendor
        $projects = Project::whereHas('vendors', function ($q) use ($vendor) {
                $q->where('vendors.id', $vendor->id);
            })
            ->orderBy('start_date', 'desc')
            ->get();

        return view('vendor.projects', compact('projects'));
    }
}