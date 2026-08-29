<?php

namespace App\Http\Controllers;

use App\Models\Users;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id = null)
    {
        $targetId = $id ?? Auth::id();

        $user = Users::where('users.user_id', $targetId)
            ->select([
                'users.user_id',
                'users.username',
                'users.user_photo',
                'users.bio',
                'users.gender',
                'users.email',
                'users.created_at',
                'users.role',
            ])
            ->selectRaw('
                COALESCE((
                    SELECT COUNT(u.content_no)
                    FROM post p
                    JOIN upvote u ON u.content_no = p.post_id AND u.content_type = ?
                    WHERE p.user_id = users.user_id
                ), 0) AS post_upvotes', ['post'])
            ->selectRaw('
                COALESCE((
                    SELECT COUNT(u.content_no)
                    FROM comment c
                    JOIN upvote u ON u.content_no = c.comment_id AND u.content_type = ?
                    WHERE c.user_id = users.user_id
                ), 0) AS comment_upvotes', ['comment'])
            ->selectRaw('
                COALESCE((
                    SELECT COUNT(u.content_no)
                    FROM post p
                    JOIN upvote u ON u.content_no = p.post_id AND u.content_type = ?
                    WHERE p.user_id = users.user_id
                ), 0) +
                COALESCE((
                    SELECT COUNT(u.content_no)
                    FROM comment c
                    JOIN upvote u ON u.content_no = c.comment_id AND u.content_type = ?
                    WHERE c.user_id = users.user_id
                ), 0) AS total_upvotes', ['post', 'comment'])
            ->first();

        return Inertia::render('User/Profile', [
            'profile_data' => Inertia::defer(fn() => $user)
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $id = Auth::id();
        if (!$id) {
            return redirect()->back()->with('error','You must be logged in to update your profile.');
        }

        $rules = [
            'username' => ['required', 'string'],
            'gender' => ['required', 'in:Male,Female'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required_if:password_change,true', 'min:8'],
            'c_password' => ['required_if:password_change,true', 'same:password'],
            'password_change' => ['nullable', 'boolean'],
            'enable_auth' => ['nullable', 'boolean'],
        ];

        $messages = [
            'username.required' => 'Username is a required field.',
            'gender.required' => 'Gender is a required field.',
            'email.required' => 'Email is a required field.',
            'email.email' => 'Email is invalid.',
            'email.unique' => 'Email already exists.',
            'password.required' => 'Password is a required field.',
            'password.min' => 'Password must be at least 8 characters.',
            'c_password.required' => 'Confirm password is a required field.',
            'c_password.same' => 'Password does not match with confirm password.',
        ];
        
        // Validate the input
        $validator = $request->validate($rules, $messages);

        if ($request->password_change) {
            Users::where('user_id', $id)->update([
                'username' => $validator['username'],
                'gender' => $validator['gender'],
                'email' => $validator['email'],
                'password' => Hash::make($validator['password']),
                'enable_auth' => $validator['enable_auth'],
            ]);
        } else {
            Users::where('user_id', $id)->update([
                'username' => $validator['username'],
                'gender' => $validator['gender'],
                'email' => $validator['email'],
                'enable_auth' => $validator['enable_auth'],
            ]);
        }

        return redirect()->back()->with('success', 'Profile updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
