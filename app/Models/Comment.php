<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Comment extends Model
{
    use HasFactory;

    protected $table = 'comment';

    protected $primaryKey = 'comment_id';

    protected $fillable = [
        'description',
        'comment_photo',
        'user_id',
        'post_id',
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

    public function post()
    {
        return $this->belongsTo(Post::class, 'post_id', 'post_id');
    }
}
