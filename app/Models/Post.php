<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Post extends Model
{
    use HasFactory;

    protected $table = 'post';

    protected $primaryKey = 'post_id';

    protected $fillable = [
        'title',
        'description',
        'post_photo',
        'user_id',
    ];

    protected function casts(): array {
        return [
            'created_at' => 'datetime:Y-m-d H:i:s',
        ];
    }

    public function user()
    {
        return $this->belongsTo(Users::class, 'user_id', 'user_id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class, 'post_id', 'post_id');
    }

    public function bookmarks()
    {
        return $this->hasMany(Bookmark::class, 'post_id', 'post_id');
    }
}
