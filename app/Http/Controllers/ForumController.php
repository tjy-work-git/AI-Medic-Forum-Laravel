<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ForumController extends Controller
{
    public function index()
    {
        $posts = Post::with("user")->orderBy("created_at", "desc")->paginate(10);

        return Inertia::render("Forums/Index", [
            "posts" => $posts
        ]);
    }

    public function create()
    {
        return Inertia::render("Forums/Post/Create");
    }

    public function store_post(Request $request)
    {
        //
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
            ->first();

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
