<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $fillable = [
        'project_id',
        'client_id',
        'title',
        'file_path',
        'is_signed',
        'signed_file_path'
    ];

    public function project() {
        return $this->belongsTo(Project::class);
    }

    public function client() {
        return $this->belongsTo(User::class, 'client_id');
    }
}