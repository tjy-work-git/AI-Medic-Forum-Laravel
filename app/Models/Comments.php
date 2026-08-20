<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Comments extends Model
{
    use HasFactory;

    protected $table = 'comments';

    protected $primaryKey = 'comment_id';

    protected $fillable = [
        'description',
        'comment_photo',
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
