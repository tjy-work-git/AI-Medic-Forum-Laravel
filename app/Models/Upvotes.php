<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Upvotes extends Model
{
    protected $table = 'upvotes';

    // No auto-incrementing primary key
    public $incrementing = false;

    protected $fillable = [
        'user_id',
        'content_type',
        'content_no',
    ];

    public function user()
    {
        return $this->belongsTo(Users::class, 'user_id', 'user_id');
    }
}
