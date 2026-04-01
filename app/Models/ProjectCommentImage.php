<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectCommentImage extends Model
{
    protected $fillable = ['project_comment_id', 'image'];

    public function comment()
    {
        return $this->belongsTo(ProjectComment::class, 'project_comment_id');
    }
}