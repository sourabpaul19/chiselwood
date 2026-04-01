<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

use App\Models\User;
use App\Models\InventoryVendor;
use App\Models\InventoryVendorCategory;

class InventoryVendorController extends Controller
{
    /* =====================================================
        INDEX
    ===================================================== */
    public function index(Request $request)
    {
        $query = User::with('inventoryVendor.category')
            ->where('role','inventory_vendor');

        if ($request->status) {
            $query->where('status',$request->status);
        }

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name','like','%'.$request->search.'%')
                  ->orWhere('email','like','%'.$request->search.'%');
            });
        }

        if ($request->filled('vendor_category_id')) {
            $query->whereHas('inventoryVendor', function ($q) use ($request) {
                $q->where('vendor_category_id',$request->vendor_category_id);
            });
        }

        $vendors = $query->latest()->paginate(20)->withQueryString();

        return view('admin.inventory-vendors.index',[
            'vendors'    => $vendors,
            'categories' => InventoryVendorCategory::where('status','active')->get(),
        ]);
    }

    /* =====================================================
        CREATE
    ===================================================== */
    public function create()
    {
        return view('admin.inventory-vendors.create',[
            'categories' => InventoryVendorCategory::where('status','active')->get(),
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
            'vendor_category_id'  => 'required|exists:inventory_vendor_categories,id',
            'status'              => 'required|in:active,inactive',
            'document'            => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'address'             => 'nullable|string|max:500',
            'vendor_state'        => 'required|string|max:50',

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

            /* USER */
            $user = User::create([
                'name'     => $request->name,
                'email'    => $request->email,
                'password' => Hash::make('vendor@123'),
                'role'     => 'inventory_vendor',
                'status'   => $request->status,
            ]);

            /* FILE */
            $doc = null;
            if ($request->hasFile('document')) {
                $doc = $request->file('document')
                    ->store('inventory-vendors/documents','public');
            }

            /* INVENTORY VENDOR */
            $vendor = InventoryVendor::create([
                'user_id'            => $user->id,
                'contact_person'     => $request->contact_person,
                'phone'              => $request->phone,
                'email'              => $request->email,
                'address'            => $request->address,
                'pincode'            => $request->pincode,
                'inventory_vendor_state'       => $request->vendor_state,
                'gstin'              => $request->gstin,
                'cin'                => $request->cin,
                'inventory_vendor_category_id' => $request->vendor_category_id,
                'notes'              => $request->notes,
                'document'           => $doc,
                'status'             => $request->status,
            ]);

            /* GENERATE ID */
            $vendor->update([
                'vendor_id' => 'IVND'.str_pad($vendor->id,5,'0',STR_PAD_LEFT)
            ]);
        });

        return redirect()
            ->route('admin.inventory-vendors.index')
            ->with('success','Inventory vendor added successfully');
    }

    /* =====================================================
        EDIT
    ===================================================== */
    public function edit(User $user)
    {
        abort_if($user->role !== 'inventory_vendor', 404);

        $user->load('inventoryVendor');

        $categories = InventoryVendorCategory::where('status','active')->get();

        return view('admin.inventory-vendors.edit', compact(
            'user',
            'categories'
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

        $user = User::with('inventoryVendor')
            ->where('role','inventory_vendor')
            ->findOrFail($id);

        $vendor = $user->inventoryVendor;

        $request->validate([
            'name'                => 'required|string|max:255',
            'phone'               => 'nullable|string|max:20',
            'vendor_category_id'  => 'required|exists:inventory_vendor_categories,id',
            'status'              => 'required|in:active,inactive',
            'document'            => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'address'             => 'nullable|string|max:500',
            'vendor_state'        => 'required|string|max:50',

            'gstin' => [
                'nullable',
                'regex:/^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z][1-9A-Z]Z[0-9A-Z]$/',
                'unique:inventory_vendors,gstin,' . $vendor->id
            ],
            'cin' => [
                'nullable',
                'regex:/^[LU][0-9]{5}[A-Z]{2}[0-9]{4}[A-Z]{3}[0-9]{6}$/',
                'unique:inventory_vendors,cin,' . $vendor->id
            ],
            'pincode' => [
                'required',
                'regex:/^[1-9][0-9]{5}$/'
            ],
        ]);

        DB::transaction(function () use ($request, $user, $vendor) {

            $user->update([
                'name'   => $request->name,
                'status' => $request->status,
            ]);

            if ($request->hasFile('document')) {

                if ($vendor->document) {
                    Storage::disk('public')->delete($vendor->document);
                }

                $vendor->document = $request->file('document')
                    ->store('inventory-vendors/documents', 'public');
            }

            $vendor->update([
                'contact_person'     => $request->contact_person,
                'phone'              => $request->phone,
                'inventory_vendor_category_id' => $request->vendor_category_id,
                'notes'              => $request->notes,
                'status'             => $request->status,
                'pincode'            => $request->pincode,
                'inventory_vendor_state'       => $request->vendor_state,
                'gstin'              => $request->gstin,
                'cin'                => $request->cin,
                'address'            => $request->address,
            ]);
        });

        return redirect()
            ->route('admin.inventory-vendors.index')
            ->with('success','Inventory vendor updated successfully');
    }

    /* =====================================================
        STATUS TOGGLE
    ===================================================== */
    public function toggleStatus(User $user)
    {
        abort_if($user->role !== 'inventory_vendor', 404);

        $status = $user->status === 'active' ? 'inactive' : 'active';

        $user->update(['status'=>$status]);
        $user->inventoryVendor?->update(['status'=>$status]);

        return back()->with('success','Status updated');
    }

    /* =====================================================
        DELETE
    ===================================================== */
    public function destroy(User $user)
    {
        abort_if($user->role !== 'inventory_vendor', 404);

        DB::transaction(function () use ($user) {
            $user->delete();
            $user->inventoryVendor()?->delete();
        });

        return back()->with('success','Moved to trash');
    }

    /* =====================================================
        TRASH / RESTORE / FORCE
    ===================================================== */
    public function trash()
    {
        $users = User::onlyTrashed()
            ->where('role','inventory_vendor')
            ->with('inventoryVendor')
            ->get();

        return view('admin.inventory-vendors.trash',compact('users'));
    }

    public function restore($id)
    {
        $user = User::onlyTrashed()
            ->where('role','inventory_vendor')
            ->findOrFail($id);

        DB::transaction(function () use ($user) {
            $user->restore();
            $user->inventoryVendor()?->restore();
        });

        return back()->with('success','Restored successfully');
    }

    public function force($id)
    {
        $user = User::onlyTrashed()
            ->where('role','inventory_vendor')
            ->with('inventoryVendor')
            ->findOrFail($id);

        DB::transaction(function () use ($user) {

            if ($user->inventoryVendor?->document) {
                Storage::disk('public')->delete($user->inventoryVendor->document);
            }

            $user->inventoryVendor()?->forceDelete();
            $user->forceDelete();
        });

        return back()->with('success','Deleted permanently');
    }
}