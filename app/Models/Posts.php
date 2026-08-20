<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Posts extends Model
{
    use HasFactory;

    protected $table = 'posts';

    protected $primaryKey = 'post_id';

    protected $fillable = [
        'title',
        'description',
        'post_photo',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(Users::class, 'user_id', 'user_id');
    }

    public function comments()
    {
        return $this->hasMany(Comments::class, 'post_id', 'post_id');
    }

    public function bookmarks()
    {
        return $this->hasMany(Bookmarks::class, 'post_id', 'post_id');
    }
}
