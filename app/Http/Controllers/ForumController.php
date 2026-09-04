<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ForumController extends Controller
{
    public function index()
    {
        $posts = Post::leftJoin("users", "users.user_id", "post.user_id")
            ->leftJoin('upvote', 'post.post_id', 'upvote.content_no')
            ->select("post.*", "users.username", "users.user_photo")
            ->selectRaw("COUNT(upvote.content_no) as upvotes")
            ->groupBy("post.post_id", "users.username", "users.user_photo")
            ->orderBy("post.created_at", "desc")
            ->paginate(10);

        return Inertia::render("Forums/Index", [
            "posts" => $posts
        ]);
    }

    public function create()
    {
        return Inertia::render("Forums/Create");
    }

    public function store_post(Request $request)
    {
        $rules = [
            'title' => ['required', 'string'],
            'description' => ['required', 'string'],
            'img' => ['nullable', 'file', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
        ];

        $messages = [
            'title.required' => 'Please enter a title.',
            'description.required' => 'Please fill in the descriptions.',
            'img.file' => 'Image must be a file.',
            'img.mimes' => 'Image must be a valid image format.',
            'img.max' => 'Image must be less than 2MB.',
        ];

        $validator = $request->validate($rules, $messages);

        $data = [
            'title' => $validator['title'],
            'description' => $validator['description'],
            'user_id' => Auth::id(),
        ];

        if (!empty($validator['img'])) {
            $data['img'] = $validator['img'];
        }

        Post::create($data);

        return redirect()->route("forum")->with("success", "Post created successfully!");
    }

    public function store_comment(Request $request)
    {
        //
    }

    public function show(string $id)
    {
        $data = Post::where('post_id', $id)
            ->leftJoin("users", "users.user_id", "post.user_id")
            ->select("post.*", "users.username", "users.user_photo", "users.bio", "users.gender")
            ->paginate(10);

        return Inertia::render("Forums/Post/Show", [
            "post" => $data
        ]);
    }

    public function edit(string $id)
    {
        //
    }

    public function update(Request $request, string $id)
    {
        //
    }

    public function destroy(string $id)
    {
        //
    }
}
