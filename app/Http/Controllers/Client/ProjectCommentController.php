<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectComment;
use App\Models\ProjectCommentImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectCommentController extends Controller
{
    // Show project details with comments
    public function show($projectId)
    {
        $project = Project::findOrFail($projectId);

        // Eager load top-level comments + user + replies
        $comments = ProjectComment::where('project_id', $projectId)
            ->whereNull('parent_id')
            ->with(['user', 'images', 'replies.user', 'replies.images'])
            ->latest()
            ->get();

        return view('client.projects.comments', compact('project', 'comments'));
    }

    // Store comment or reply
    public function store(Request $request)
    {
        $request->validate([
            'project_id' => 'required',
            'comment' => 'required|string',
            'images.*' => 'image|mimes:jpg,jpeg,png|max:2048'
        ]);

        $comment = ProjectComment::create([
            'project_id' => $request->project_id,
            'user_id' => Auth::id(),
            'user_type' => Auth::user()->role, // assuming role: vendor/client/admin
            'comment' => $request->comment,
            'parent_id' => $request->parent_id ?? null
        ]);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('comment-images', 'public');
                ProjectCommentImage::create([
                    'project_comment_id' => $comment->id,
                    'image' => $path
                ]);
            }
        }

        return back()->with('success', 'Comment added successfully.');
    }

    // Update comment
    public function update(Request $request, $id)
    {
        $comment = ProjectComment::findOrFail($id);

        $request->validate([
            'comment' => 'required|string',
            'images.*' => 'image|mimes:jpg,jpeg,png|max:2048'
        ]);

        $comment->update(['comment' => $request->comment]);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('comment-images', 'public');
                ProjectCommentImage::create([
                    'project_comment_id' => $comment->id,
                    'image' => $path
                ]);
            }
        }

        return back()->with('success', 'Comment updated successfully.');
    }

    // Delete comment
    public function destroy($id)
    {
        $comment = ProjectComment::findOrFail($id);

        foreach ($comment->images as $img) {
            \Storage::disk('public')->delete($img->image);
        }

        $comment->delete();

        return back()->with('success', 'Comment deleted successfully.');
    }
}