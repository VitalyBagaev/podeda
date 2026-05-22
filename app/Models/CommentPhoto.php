<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommentPhoto extends Model
{
    protected $fillable = [
        'comment_id',
        'photo_path',
        ];
    public $timestamps = true;

    public function comment()
    {
        return $this->belongsTo(Comment::class);
    }
}
