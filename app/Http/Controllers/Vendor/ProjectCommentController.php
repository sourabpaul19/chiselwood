<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProjectComment;
use App\Models\Project;
use App\Models\Vendor;
use App\Models\ProjectCommentImage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProjectCommentController extends Controller
{
    public function index($projectId)
    {
        $userId = Auth::id();

        $vendor = Vendor::where('user_id', $userId)->firstOrFail();

        $project = Project::where('id', $projectId)
            ->whereHas('vendors', function ($q) use ($vendor) {
                $q->where('vendors.id', $vendor->id);
            })
            ->firstOrFail();

        $comments = ProjectComment::where('project_id', $projectId)
            ->with(['replies', 'images'])
            ->whereNull('parent_id')
            ->latest()
            ->get();

        return view('vendor.project-comments', compact('project', 'comments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'project_id' => 'required',
            'comment' => 'required',
            'images.*' => 'image|mimes:jpg,jpeg,png|max:2048'
        ]);

        $comment = ProjectComment::create([
            'project_id' => $request->project_id,
            'user_id' => Auth::id(),
            'user_type' => 'vendor',
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

        return back()->with('success', 'Comment added');
    }

    public function update(Request $request, $id)
    {
        $comment = ProjectComment::with('images')->findOrFail($id);

        // ✅ Only owner can edit
        if ($comment->user_id != Auth::id()) {
            abort(403);
        }

        $request->validate([
            'comment' => 'required',
            'images.*' => 'image|mimes:jpg,jpeg,png|max:2048'
        ]);

        $comment->update([
            'comment' => $request->comment
        ]);

        // Add new images
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('comment-images', 'public');

                ProjectCommentImage::create([
                    'project_comment_id' => $comment->id,
                    'image' => $path
                ]);
            }
        }

        return back()->with('success', 'Comment updated');
    }

    public function destroy($id)
    {
        $comment = ProjectComment::with('images')->findOrFail($id);

        // ✅ Only owner can delete
        if ($comment->user_id != Auth::id()) {
            abort(403);
        }

        foreach ($comment->images as $img) {
            Storage::disk('public')->delete($img->image);
        }

        $comment->delete();

        return back()->with('success', 'Comment deleted');
    }

    public function deleteImage($id)
    {
        $image = ProjectCommentImage::findOrFail($id);

        Storage::disk('public')->delete($image->image);

        $image->delete();

        return back()->with('success', 'Image deleted');
    }
}