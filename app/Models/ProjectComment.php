<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectComment extends Model
{
    protected $fillable = [
        'project_id',
        'user_id',
        'user_type',
        'comment',
        'parent_id'
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function replies()
    {
        return $this->hasMany(ProjectComment::class, 'parent_id');
    }

    public function parent()
    {
        return $this->belongsTo(ProjectComment::class, 'parent_id');
    }
    public function images()
    {
        return $this->hasMany(ProjectCommentImage::class);
    }
    // Polymorphic-like user relationship
    // ProjectComment.php
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }
}