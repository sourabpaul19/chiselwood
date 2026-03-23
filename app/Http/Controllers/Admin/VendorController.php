<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

use App\Models\User;
use App\Models\Vendor;
use App\Models\VendorCategory;
use App\Models\Project;

class VendorController extends Controller
{
    /* =====================================================
        INDEX
    ===================================================== */
    public function index(Request $request)
    {
        $query = User::with('vendor.category','vendor.projects')
            ->where('role','vendor');

        if ($request->status) {
            $query->where('status',$request->status);
        }

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name','like','%'.$request->search.'%')
                  ->orWhere('email','like','%'.$request->search.'%');
            });
        }

        // Vendor category filter
        if ($request->filled('vendor_category_id')) {
            $query->whereHas('vendor', function ($q) use ($request) {
                $q->where('vendor_category_id',$request->vendor_category_id);
            });
        }

        $vendors = $query->latest()->paginate(20)->withQueryString();

        return view('admin.vendors.index',[
            'vendors' => $vendors,
            'categories' => VendorCategory::where('status','active')->get(),
        ]);
    }

    /* =====================================================
        CREATE
    ===================================================== */
    public function create()
    {
        return view('admin.vendors.create',[
            'categories' => VendorCategory::where('status','active')->get(),
            'projects'   => Project::where('status','active')->get(),
        ]);
    }

    /* =====================================================
        STORE
    ===================================================== */
    public function store(Request $request)
    {
        $request->merge([
            'gstin' => strtoupper($request->gstin),
            'cin'   => strtoupper($request->cin),
        ]);

        $request->validate([
            'name'                => 'required|string|max:255',
            'email'               => 'required|email|unique:users,email',
            'phone'               => 'nullable|string|max:20',
            'vendor_category_id'  => 'required|exists:vendor_categories,id',
            'status'              => 'required|in:active,inactive',
            'document'            => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'address'      => 'nullable|string|max:500',
            'vendor_state' => 'required|string|max:50',

            /* TAX / LEGAL */
            'gstin' => [
                'nullable',
                'regex:/^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z][1-9A-Z]Z[0-9A-Z]$/'
            ],
            'cin' => [
                'nullable',
                'regex:/^[LU][0-9]{5}[A-Z]{2}[0-9]{4}[A-Z]{3}[0-9]{6}$/'
            ],
            'pincode' => [
                'required',
                'regex:/^[1-9][0-9]{5}$/'
            ],
        ]);

        DB::transaction(function () use ($request) {

            /* ======================
               CREATE USER
            ====================== */
            $user = User::create([
                'name'     => $request->name,
                'email'    => $request->email,
                'password' => Hash::make('vendor@123'),
                'role'     => 'vendor',
                'status'   => $request->status,
            ]);

            /* ======================
               FILE UPLOAD
            ====================== */
            $doc = null;
            if ($request->hasFile('document')) {
                $doc = $request->file('document')
                    ->store('vendor/documents','public');
            }

            /* ======================
               CREATE VENDOR
            ====================== */
            $vendor = Vendor::create([
                'user_id'            => $user->id,
                'contact_person'     => $request->contact_person,
                'phone'              => $request->phone,
                'email'              => $request->email,
                'address'            => $request->address,
                'pincode'      => $request->pincode,
                'vendor_state' => $request->vendor_state,
                'gstin'        => $request->gstin,
                'cin'          => $request->cin,
                'vendor_category_id' => $request->vendor_category_id,
                'notes'              => $request->notes,
                'document'           => $doc,
                'status'             => $request->status,
            ]);

            /* ======================
               GENERATE VENDOR ID
            ====================== */
            $vendor->update([
                'vendor_id' => 'VND'.str_pad($vendor->id,5,'0',STR_PAD_LEFT)
            ]);

            /* ======================
               ATTACH PROJECTS
            ====================== */
            if ($request->projects) {
                $vendor->projects()->sync($request->projects);
            }


        });

        return redirect()
            ->route('admin.vendors.index')
            ->with('success','Vendor added successfully');
    }

    /* =====================================================
        EDIT
    ===================================================== */
    // public function edit(User $user)
    // {
    //     abort_if($user->role !== 'vendor', 404);

    //     return view('admin.vendors.edit',[
    //         'user'       => $user,
    //         'categories' => VendorCategory::where('status','active')->get(),
    //         'projects'   => Project::where('status','active')->get(),
    //     ]);
    // }

    public function edit(User $user)
    {
        abort_if($user->role !== 'vendor', 404);

        // Load vendor + linked projects
        $user->load('vendor.projects');

        $categories = VendorCategory::where('status', 'active')->get();
        $projects   = Project::where('status', 'active')->get();

        // Collect linked project IDs for selected state
        $linkedProjectIds = $user->vendor
            ? $user->vendor->projects->pluck('id')->toArray()
            : [];

        return view('admin.vendors.edit', compact(
            'user',
            'categories',
            'projects',
            'linkedProjectIds'
        ));
    }

    /* =====================================================
        UPDATE
    ===================================================== */
    public function update(Request $request, $id)
{
    $request->merge([
        'gstin' => strtoupper($request->gstin),
        'cin'   => strtoupper($request->cin),
    ]);

    $user = User::with('vendor.projects')
        ->where('role', 'vendor')
        ->findOrFail($id);

    $vendor = $user->vendor;

    $request->validate([
        'name'                => 'required|string|max:255',
        'phone'               => 'nullable|string|max:20',
        'vendor_category_id'  => 'required|exists:vendor_categories,id',
        'status'              => 'required|in:active,inactive',
        'notes'               => 'nullable|string',
        'document'            => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        'projects'            => 'nullable|array',
        'address'      => 'nullable|string|max:500',
        'projects.*'          => 'exists:projects,id',
        'vendor_state' => 'required|string|max:50',

        /* TAX / LEGAL */
        'gstin' => [
            'nullable',
            'regex:/^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z][1-9A-Z]Z[0-9A-Z]$/',
            'unique:vendors,gstin,' . $user->vendor->id
        ],
        'cin' => [
            'nullable',
            'regex:/^[LU][0-9]{5}[A-Z]{2}[0-9]{4}[A-Z]{3}[0-9]{6}$/',
            'unique:vendors,cin,' . $user->vendor->id
        ],
        'pincode' => [
            'required',
            'regex:/^[1-9][0-9]{5}$/'
        ],
    ]);

    DB::transaction(function () use ($request, $user, $vendor) {

        /* ==========================
           UPDATE USER (EMAIL FIXED)
        ========================== */
        $user->update([
            'name'   => $request->name,
            'status' => $request->status,
        ]);

        /* ==========================
           DOCUMENT UPLOAD
        ========================== */
        if ($request->hasFile('document')) {

            // delete old document
            if ($vendor->document) {
                Storage::disk('public')->delete($vendor->document);
            }

            $vendor->document = $request->file('document')
                ->store('vendors/documents', 'public');
        }

        /* ==========================
           UPDATE VENDOR
        ========================== */
        $vendor->update([
            'name'               => $request->name,
            'contact_person'     => $request->contact_person,
            'phone'              => $request->phone,
            'vendor_category_id' => $request->vendor_category_id,
            'notes'              => $request->notes,
            'status'             => $request->status,
            'pincode'      => $request->pincode,
                'vendor_state' => $request->vendor_state,
                'gstin'        => $request->gstin,
                'cin'          => $request->cin,
                'address'            => $request->address,
        ]);

        /* ==========================
           SYNC PROJECTS
        ========================== */
        $vendor->projects()->sync($request->projects ?? []);
    });

    return redirect()
        ->route('admin.vendors.index')
        ->with('success', 'Vendor updated successfully');
}


    /* =====================================================
        STATUS TOGGLE
    ===================================================== */
    public function toggleStatus(User $user)
    {
        abort_if($user->role !== 'vendor', 404);

        $status = $user->status === 'active' ? 'inactive' : 'active';

        $user->update(['status'=>$status]);
        $user->vendor?->update(['status'=>$status]);

        return back()->with('success','Status updated');
    }

    /* =====================================================
        DELETE (SOFT)
    ===================================================== */
    public function destroy(User $user)
    {
        abort_if($user->role !== 'vendor', 404);

        DB::transaction(function () use ($user) {
            $user->delete();
            $user->vendor()?->delete();
        });

        return redirect()
            ->route('admin.vendors.index')
            ->with('success','Vendor moved to trash');
    }

    /* =====================================================
        TRASH
    ===================================================== */
    public function trash()
    {
        $users = User::onlyTrashed()
            ->where('role','vendor')
            ->with('vendor')
            ->get();

        return view('admin.vendors.trash',compact('users'));
    }

    public function show(User $user)
{
    $user->load([
        'vendor.category',
        'vendor.projects'
    ]);

    return view('admin.vendors.show', compact('user'));
}




    /* =====================================================
        RESTORE
    ===================================================== */
    public function restore($id)
    {
        $user = User::onlyTrashed()
            ->where('role','vendor')
            ->findOrFail($id);

        DB::transaction(function () use ($user) {
            $user->restore();
            $user->vendor()?->restore();
        });

        return back()->with('success','Vendor restored successfully');
    }

    /* =====================================================
        FORCE DELETE
    ===================================================== */
    public function force($id)
    {
        $user = User::onlyTrashed()
            ->where('role','vendor')
            ->with('vendor')
            ->findOrFail($id);

        DB::transaction(function () use ($user) {

            if ($user->vendor?->document) {
                Storage::disk('public')->delete($user->vendor->document);
            }

            $user->vendor()?->forceDelete();
            $user->forceDelete();
        });

        return back()->with('success','Vendor permanently deleted');
    }
}