<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ClientController extends Controller
{
    /* ======================
    LIST CLIENTS
    ====================== */
    // public function index(Request $request)
    // {
    //     $query = User::with('client')
    //         ->where('role', 'client');

    //     if ($request->filled('status')) {
    //         $query->where('status', $request->status);
    //     }

    //     if ($request->filled('search')) {
    //         $query->where(function ($q) use ($request) {
    //             $q->where('name', 'like', '%' . $request->search . '%')
    //               ->orWhere('email', 'like', '%' . $request->search . '%');
    //         });
    //     }

    //     $clients = $query
    //         ->latest()
    //         ->paginate(10)
    //         ->withQueryString();

    //     return view('admin.clients.index', compact('clients'));
    // }

    public function index(Request $request)
    {
        $query = User::with([
                'client.projects' // 👈 THIS IS THE KEY
            ])
            ->where('role', 'client');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        $clients = $query
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.clients.index', compact('clients'));
    }


    /* ======================
    CREATE FORM
    ====================== */
    public function create()
    {
        return view('admin.clients.create');
    }

    /* ======================
    STORE CLIENT
    ====================== */
    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'name'  => 'required|string|max:255',
    //         'email' => 'required|email|unique:users,email',
    //         'status'=> 'required|in:active,inactive',
    //         'client_state' => 'required|string|max:50',
    //         'document' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
    //     ]);

    //     DB::transaction(function () use ($request) {

    //         /* ========= USER ========= */
    //         $user = User::create([
    //             'name'     => $request->name,
    //             'email'    => $request->email,
    //             'password' => Hash::make('client@123'),
    //             'role'     => 'client',
    //             'status'   => $request->status,
    //         ]);

    //         /* ========= FILE ========= */
    //         $document = null;
    //         if ($request->hasFile('document')) {
    //             $document = $request->file('document')
    //                 ->store('clients/documents', 'public');
    //         }

    //         /* ========= CLIENT ========= */
    //         $client = Client::create([
    //             'user_id'      => $user->id,
    //             'company_name' => $request->company_name,
    //             'phone'        => $request->phone,
    //             'address'      => $request->address,
    //             'client_state' => $request->client_state, // ✅ ADD
    //             'social_media' => $request->social_media,
    //             'preferred_communication' => $request->preferred_communication,
    //             'budget_range' => $request->budget_range,
    //             'notes'        => $request->notes,
    //             'document'     => $document,
    //             'status'       => $request->status,
    //         ]);

    //         $client->update([
    //             'client_id' => 'CLINT' . str_pad($client->id, 5, '0', STR_PAD_LEFT),
    //         ]);
    //     });

    //     return redirect()
    //         ->route('admin.clients.index')
    //         ->with('success', 'Client added successfully');
    // }

    public function store(Request $request)
    {
        $request->merge([
            'gstin' => strtoupper($request->gstin),
            'cin'   => strtoupper($request->cin),
        ]);

        $request->validate([
            /* USER */
            'name'   => 'required|string|max:255',
            'email'  => 'required|email|unique:users,email',
            'status' => 'required|in:active,inactive',

            /* CLIENT */
            'company_name' => 'required|string|max:255',
            'phone'        => 'nullable|string|max:20',
            'address'      => 'nullable|string|max:500',
            'client_state' => 'required|string|max:50',

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

            /* FILE */
            'document' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        DB::transaction(function () use ($request) {

            /* ========= USER ========= */
            $user = User::create([
                'name'     => $request->name,
                'email'    => $request->email,
                'password' => Hash::make('client@123'),
                'role'     => 'client',
                'status'   => $request->status,
            ]);

            /* ========= FILE ========= */
            $document = null;
            if ($request->hasFile('document')) {
                $document = $request->file('document')
                    ->store('clients/documents', 'public');
            }

            /* ========= CLIENT ========= */
            $client = Client::create([
                'user_id'      => $user->id,
                'company_name' => $request->company_name,
                'phone'        => $request->phone,
                'address'      => $request->address,
                'pincode'      => $request->pincode,
                'client_state' => $request->client_state,
                'gstin'        => $request->gstin,
                'cin'          => $request->cin,
                'social_media' => $request->social_media,
                'preferred_communication' => $request->preferred_communication,
                'budget_range' => $request->budget_range,
                'notes'        => $request->notes,
                'document'     => $document,
                'status'       => $request->status,
            ]);

            $client->update([
                'client_id' => 'CLINT' . str_pad($client->id, 5, '0', STR_PAD_LEFT),
            ]);
        });

        return redirect()
            ->route('admin.clients.index')
            ->with('success', 'Client added successfully');
    }

    /* ======================
    SHOW CLIENT
    ====================== */
    public function show(User $client)
    {
        abort_if($client->role !== 'client', 404);

        $client->load('client');

        return view('admin.clients.show', compact('client'));
    }

    /* ======================
    EDIT FORM
    ====================== */
    public function edit(User $client)
    {
        abort_if($client->role !== 'client', 404);

        $client->load('client');

        return view('admin.clients.edit', compact('client'));
    }

    /* ======================
    UPDATE CLIENT
    ====================== */
    // public function update(Request $request, User $client)
    // {
    //     abort_if($client->role !== 'client', 404);

    //     $request->validate([
    //         'name'   => 'required|string|max:255',
    //         'status' => 'required|in:active,inactive',
    //         'client_state' => 'required|string|max:50',
    //         'document' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
    //     ]);

    //     DB::transaction(function () use ($request, $client) {

    //         /* ========= USER ========= */
    //         $client->update([
    //             'name'   => $request->name,
    //             'status' => $request->status,
    //         ]);

    //         /* ========= CLIENT ========= */
    //         $profile = $client->client;

    //         if ($request->hasFile('document')) {

    //             if ($profile->document) {
    //                 Storage::disk('public')->delete($profile->document);
    //             }

    //             $profile->document = $request->file('document')
    //                 ->store('clients/documents', 'public');
    //         }

    //         $profile->update([
    //             'company_name' => $request->company_name,
    //             'phone'        => $request->phone,
    //             'address'      => $request->address,
    //             'client_state' => $request->client_state, // ✅ ADD
    //             'social_media' => $request->social_media,
    //             'preferred_communication' => $request->preferred_communication,
    //             'budget_range' => $request->budget_range,
    //             'notes'        => $request->notes,
    //             'status'       => $request->status,
    //         ]);
    //     });

    //     return redirect()
    //         ->route('admin.clients.index')
    //         ->with('success', 'Client added successfully');
    // }

    public function update(Request $request, User $client)
{
    abort_if($client->role !== 'client', 404);

    // Normalize uppercase fields
    $request->merge([
        'gstin' => strtoupper($request->gstin),
        'cin'   => strtoupper($request->cin),
    ]);

    $request->validate([
        /* USER */
        'name'   => 'required|string|max:255',
        'status' => 'required|in:active,inactive',

        /* CLIENT */
        'company_name' => 'required|string|max:255',
        'phone'        => 'nullable|string|max:20',
        'address'      => 'nullable|string|max:500',
        'client_state' => 'required|string|max:50',

        /* TAX / LEGAL */
        'gstin' => [
            'nullable',
            'regex:/^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z][1-9A-Z]Z[0-9A-Z]$/',
            'unique:clients,gstin,' . $client->client->id
        ],
        'cin' => [
            'nullable',
            'regex:/^[LU][0-9]{5}[A-Z]{2}[0-9]{4}[A-Z]{3}[0-9]{6}$/',
            'unique:clients,cin,' . $client->client->id
        ],
        'pincode' => [
            'required',
            'regex:/^[1-9][0-9]{5}$/'
        ],

        /* FILE */
        'document' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
    ]);

    DB::transaction(function () use ($request, $client) {

        /* ========= USER ========= */
        $client->update([
            'name'   => $request->name,
            'status' => $request->status,
        ]);

        /* ========= CLIENT ========= */
        $profile = $client->client;

        if ($request->hasFile('document')) {

            if ($profile->document) {
                Storage::disk('public')->delete($profile->document);
            }

            $profile->document = $request->file('document')
                ->store('clients/documents', 'public');
        }

        $profile->update([
            'company_name' => $request->company_name,
            'phone'        => $request->phone,
            'address'      => $request->address,
            'pincode'      => $request->pincode,
            'client_state' => $request->client_state,
            'gstin'        => $request->gstin,
            'cin'          => $request->cin,
            'social_media' => $request->social_media,
            'preferred_communication' => $request->preferred_communication,
            'budget_range' => $request->budget_range,
            'notes'        => $request->notes,
            'status'       => $request->status,
        ]);
    });

    return redirect()
        ->route('admin.clients.index')
        ->with('success', 'Client updated successfully');
}


    /* ======================
    TOGGLE STATUS
    ====================== */
    public function toggleStatus(User $client)
    {
        abort_if($client->role !== 'client', 404);

        $client->update([
            'status' => $client->status === 'active' ? 'inactive' : 'active'
        ]);

        return back()->with('success', 'Status updated');
    }

    /* ======================
    SOFT DELETE
    ====================== */
    public function destroy(User $client)
    {
        abort_if($client->role !== 'client', 404);

        DB::transaction(function () use ($client) {
            $client->delete();
            $client->client()?->delete();
        });

        return redirect()
            ->route('admin.clients.index')
            ->with('success', 'Client moved to trash');
    }

    /* ======================
    TRASH
    ====================== */
    public function trash()
    {
        $users = User::onlyTrashed()
            ->where('role', 'client')
            ->with('client')
            ->get();

        return view('admin.clients.trash', compact('users'));
    }

    /* ======================
    RESTORE
    ====================== */
    public function restore($id)
    {
        $user = User::onlyTrashed()
            ->where('role', 'client')
            ->findOrFail($id);

        DB::transaction(function () use ($user) {
            $user->restore();
            $user->client()?->restore();
        });

        return back()->with('success', 'Client restored successfully');
    }

    /* ======================
    FORCE DELETE
    ====================== */
    public function force($id)
    {
        $user = User::onlyTrashed()
            ->where('role', 'client')
            ->with('client')
            ->findOrFail($id);

        DB::transaction(function () use ($user) {

            if ($user->client?->document) {
                Storage::disk('public')->delete($user->client->document);
            }

            $user->client()?->forceDelete();
            $user->forceDelete();
        });

        return back()->with('success', 'Client permanently deleted');
    }
}
