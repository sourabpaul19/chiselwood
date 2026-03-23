<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Models\Staff;
use App\Models\Department;
use App\Models\EmployeeType;

class StaffController extends Controller
{
    //

    public function index(Request $request)
    {
        $query = User::with('staff.department','staff.employeeType')
            ->where('role','staff');

        if ($request->status) {
            $query->where('status',$request->status);
        }

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name','like','%'.$request->search.'%')
                ->orWhere('email','like','%'.$request->search.'%');
            });
        }

        // ✅ Department filter (staff table)
        if ($request->filled('department_id')) {
            $query->whereHas('staff', function ($q) use ($request) {
                $q->where('department_id', $request->department_id);
            });
        }

        // ✅ Employee type filter (staff table)
        if ($request->filled('employee_type_id')) {
            $query->whereHas('staff', function ($q) use ($request) {
                $q->where('employee_type_id', $request->employee_type_id);
            });
        }

        $staffs = $query->latest()->paginate(20)->withQueryString();

        return view('admin.staff.index', [
            'staffs' => $staffs,
            'departments' => Department::where('status','active')->get(),
            'employeeTypes' => EmployeeType::where('status','active')->get(),
        ]);
    }

    public function create()
    {
        return view('admin.staff.create',[
            'departments' => Department::where('status','active')->get(),
            'types' => EmployeeType::where('status','active')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'              => 'required|string|max:255',
            'email'             => 'required|email|unique:users,email',
            'phone'             => 'nullable|string|max:20',
            'department_id'     => 'required|exists:departments,id',
            'employee_type_id'  => 'required|exists:employee_types,id',
            'status'            => 'required|in:active,inactive',
            'document'          => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        DB::transaction(function () use ($request) {

            /* ======================
            CREATE USER
            ====================== */
            $user = User::create([
                'name'     => $request->name,
                'email'    => $request->email,
                'password' => Hash::make('staff@123'),
                'role'     => 'staff',
                'status'   => $request->status,
            ]);

            /* ======================
            FILE UPLOAD
            ====================== */
            $doc = null;
            if ($request->hasFile('document')) {
                $doc = $request->file('document')
                    ->store('staff/documents', 'public');
            }

            /* ======================
            CREATE STAFF
            ====================== */
            $staff = Staff::create([
                'user_id'           => $user->id,
                'phone'             => $request->phone,
                'department_id'     => $request->department_id,
                'employee_type_id'  => $request->employee_type_id,
                'designation'       => $request->designation,
                'skills'            => $request->skills,
                'salary'            => $request->salary,
                'document'          => $doc,
                'notes'             => $request->notes,
                'status'            => $request->status,
            ]);

            /* ======================
            GENERATE STAFF CODE
            ====================== */
            $staff->update([
                'staff_id' => 'STF' . str_pad($staff->id, 5, '0', STR_PAD_LEFT),
            ]);
        });

        return redirect()
            ->route('admin.staff.index')
            ->with('success', 'Staff added successfully');
    }
    

    public function edit(User $user)
    {
        abort_if($user->role !== 'staff', 404);

        $departments = Department::where('status','active')->get();
        $employeeTypes = EmployeeType::where('status','active')->get();

        return view('admin.staff.edit', compact(
            'user',
            'departments',
            'employeeTypes'
        ));
    }



    public function update(Request $request, User $user)
    {
        $user->update([
            'name'=>$request->name,
            'status'=>$request->status
        ]);

        $staff = $user->staff;

        if ($request->hasFile('document')) {
            $staff->document = $request->file('document')->store('staff','public');
        }

        $staff->update($request->except(['document','email','status','name']));

        return back()->with('success','Staff updated');
    }
   
    public function toggleStatus(User $user)
    {
        $user->update([
            'status'=>$user->status === 'active' ? 'inactive' : 'active'
        ]);

        return back()->with('success','Status updated');
    }

    public function show(User $user)
    {
        $user->load('department'); 

        return view('admin.staff.show', compact('user'));
    }

    public function destroy(User $user)
    {
        abort_if($user->role !== 'staff', 404);

        DB::transaction(function () use ($user) {
            $user->delete();              // ✅ deletes user
            $user->staff()?->delete();    // ✅ deletes staff
        });

        //return back()->with('success','Staff moved to trash');
        return redirect()
            ->route('admin.staff.index')
            ->with('success', 'Staff moved to trash');
    }

    public function trash()
    {
        
        $users = User::onlyTrashed()
            ->where('role', 'staff')
            ->with('staff') // 👈 REQUIRED
            ->get();

        return view('admin.staff.trash', compact('users'));
    }

    public function restore($id)
    {
        $user = User::onlyTrashed()
            ->where('role','staff')
            ->findOrFail($id);

        DB::transaction(function () use ($user) {
            $user->restore();
            $user->staff()?->restore();
        });

        return back()->with('success','Staff restored successfully');
    }

    public function force($id)
    {
        $user = User::onlyTrashed()
            ->where('role','staff')
            ->with('staff')
            ->findOrFail($id);

        DB::transaction(function () use ($user) {

            if ($user->staff?->document) {
                Storage::disk('public')->delete($user->staff->document);
            }

            $user->staff()?->forceDelete();
            $user->forceDelete();
        });

        return back()->with('success','Staff permanently deleted');
    }






}
