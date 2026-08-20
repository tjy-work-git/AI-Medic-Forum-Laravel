<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bookmarks extends Model
{
    protected $table = 'bookmarks';

    // No auto-incrementing primary key (pivot table)
    public $incrementing = false;

    protected $fillable = [
        'user_id',
        'post_id',
    ];

    public function user()
    {
        return $this->belongsTo(Users::class, 'user_id', 'user_id');
    }

    public function post()
    {
        return $this->belongsTo(Posts::class, 'post_id', 'post_id');
    }
}
