<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Document;
use App\Models\Project;
use App\Models\User;

class DocumentController extends Controller
{
    public function index() {
        $documents = Document::with('project','client')->get();
        return view('admin.documents.index', compact('documents'));
    }

    public function create() {
        $projects = Project::all();
        $clients = User::where('role','client')->get();
        return view('admin.documents.create', compact('projects','clients'));
    }

    public function store(Request $request) {
        $request->validate([
            'title'=>'required|string',
            'project_id'=>'required|exists:projects,id',
            'client_id'=>'required|exists:users,id',
            'file'=>'required|file|mimes:pdf,doc,docx'
        ]);

        $filePath = $request->file('file')->store('documents', 'public'); 
        // 'public' disk stores in storage/app/public/documents

        Document::create([
            'title'=>$request->title,
            'project_id'=>$request->project_id,
            'client_id'=>$request->client_id,
            'file_path'=>$filePath
        ]);

        return redirect()->route('admin.documents.index')->with('success','Document uploaded successfully.');
    }

    public function signedIndex() {
        $documents = Document::where('is_signed', true)->with('project','client')->get();
        return view('admin.documents.signed', compact('documents'));
    }

    public function edit($id)
    {
        $document = Document::findOrFail($id);

        // रोक दो अगर signed है
        if ($document->is_signed) {
            return redirect()->route('admin.documents.index')
                ->with('error', 'Signed document cannot be edited.');
        }

        $projects = Project::all();
        $clients = User::where('role','client')->get();

        return view('admin.documents.edit', compact('document','projects','clients'));
    }

    public function update(Request $request, $id)
    {
        $document = Document::findOrFail($id);

        // Double protection (VERY IMPORTANT)
        if ($document->is_signed) {
            return redirect()->route('admin.documents.index')
                ->with('error', 'Signed document cannot be updated.');
        }

        $request->validate([
            'title' => 'required|string',
            'project_id' => 'required|exists:projects,id',
            'client_id' => 'required|exists:users,id',
            'file' => 'nullable|file|mimes:pdf,doc,docx'
        ]);

        $data = [
            'title' => $request->title,
            'project_id' => $request->project_id,
            'client_id' => $request->client_id,
        ];

        if ($request->hasFile('file')) {

            // delete old file
            if ($document->file_path && file_exists(storage_path('app/public/'.$document->file_path))) {
                unlink(storage_path('app/public/'.$document->file_path));
            }

            $data['file_path'] = $request->file('file')->store('documents','public');
        }

        $document->update($data);

        return redirect()->route('admin.documents.index')->with('success','Document updated successfully.');
    }
}