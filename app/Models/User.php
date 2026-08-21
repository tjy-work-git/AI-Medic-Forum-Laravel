<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UsersFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['username', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UsersFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */

    protected $table = 'user';

    protected $primaryKey = 'user_id';

    protected $fillable = [
        'username',
        'bio',
        'gender',
        'email',
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function posts()
    {
        return $this->hasMany(Posts::class, 'user_id', 'user_id');
    }

    public function comments()
    {
        return $this->hasMany(Comments::class, 'user_id', 'user_id');
    }

    public function bookmarks()
    {
        return $this->hasMany(Bookmarks::class, 'user_id', 'user_id');
    }

    public function feedbacks()
    {
        return $this->hasMany(Feedbacks::class, 'user_id', 'user_id');
    }

    public function reports()
    {
        return $this->hasMany(Reports::class, 'user_id', 'user_id');
    }

    public function upvotes()
    {
        return $this->hasMany(Upvotes::class, 'user_id', 'user_id');
    }
}
