<?php

namespace App\Http\Controllers;

use App\Models\Users;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UserController extends Controller
{
    public function index()
    {
        //
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

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

        return Inertia::render('User/Profile/Index', [
            'profile_data' => Inertia::defer(fn() => $user)
        ]);
    }

    public function edit()
    {
       return Inertia::render('User/Profile/Update', [
            'data' => Inertia::defer(fn() => Auth::user())
        ]); 
    }

    public function update(Request $request)
    {
        $id = Auth::id();
        if (!$id) {
            return redirect()->back()->with('error','You must be logged in to update your profile.');
        }

        // Standard rules and messages
        $rules = [
            'username' => ['required', 'string'],
            'bio' => ['nullable', 'string'],
            'gender' => ['required', 'in:Male,Female'],
            'password_change' => ['nullable', 'boolean'],
            'enable_auth' => ['nullable', 'boolean'],
        ];

        $messages = [
            'username.required' => 'Username is a required field.',
            'gender.required' => 'Gender is a required field.',
        ];

        // Enable rules if email change is enabled
        if ($request->email_change) {
            $rules['email'] = 'required,email,unique:users,email';
            $messages['email.required'] = 'Email is a required field.';
            $messages['email.email'] = 'Email is invalid.';
            $messages['email.unique'] = 'Email already exists.';
        }

        // Enable rules if password change is enabled
        if ($request->password_change) {
            $rules['password'] = 'required|min:8';
            $rules['c_password'] = 'required|same:password';
            $messages['password.required'] = 'Password is a required field.';
            $messages['password.min'] = 'Password must be at least 8 characters.';
            $messages['c_password.required'] = 'Confirm password is a required field.';
            $messages['c_password.same'] = 'Confirm password does not match with password.';
        }
        
        // Validate the input
        $validator = $request->validate($rules, $messages);

        // Recollect all the data from validator
        $data = [
            'username'     => $validator['username'],
            'bio'          => $validator['bio'],
            'gender'       => $validator['gender'],
            'enabled_auth' => $validator['enable_auth'] ?? false,
        ];

        // Only include this if the email change is enabled
        if (!empty($validator['email'])) {
            $data['email'] = $validator['email'];
        }

        // Only include this if the password change is enabled
        if (!empty($validator['password'])) {
            $data['password'] = Hash::make($validator['password']);
        }

        // Execute update
        Users::where('user_id', $id)->update($data);

        return redirect()->back()->with('success', 'Profile updated successfully!');
    }

    public function destroy(string $id)
    {
        //
    }
}
