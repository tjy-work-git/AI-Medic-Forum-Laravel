<?php

namespace App\Http\Controllers;

use App\Models\Bookmark;
use App\Models\Comment;
use App\Models\Post;
use App\Models\Report;
use App\Models\Upvote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ForumController extends Controller
{
    public function index()
    {
        $data = Post::leftJoin("users", "users.user_id", "post.user_id")
            ->leftJoin('upvote', function ($join) {
                $join->on('post.post_id', 'upvote.content_no')
                    ->where('upvote.content_type', 'post');
            })
            ->select("post.*", "users.username", "users.user_photo")
            ->selectRaw("COUNT(upvote.content_no) as upvotes")
            ->groupBy("post.post_id", "users.username", "users.user_photo")
            ->orderBy("post.created_at", "desc")
            ->paginate(10);

        return Inertia::render("Forums/Index", [
            "posts" => Inertia::defer(fn() => $data)
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

        $post = Post::create($data);

        return redirect("/forum/post/{$post->post_id}")->with("success", "Post created successfully!");
    }

    public function store_comment(Request $request)
    {
        $rules = [
            'description' => ['required', 'string'],
            'img' => ['nullable', 'file', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
            'post_id' => ['required', 'exists:post,post_id']
        ];

        $messages = [
            'description.required' => 'Please fill in the descriptions.',
            'post_id.exists' => 'Invalid post ID within the form.',
            'img.file' => 'Image must be a file.',
            'img.mimes' => 'Image must be a valid image format.',
            'img.max' => 'Image must be less than 2MB.',
        ];

        $validator = $request->validate($rules, $messages);

        $data = [
            'description' => $validator['description'],
            'user_id' => Auth::id(),
            'post_id' => $validator['post_id'],
        ];

        if (!empty($validator['img'])) {
            $data['img'] = $validator['img'];
        }

        Comment::create($data);

        return back();
    }

    public function show_post(string $id)
    {
        $uid = Auth::id();

        $post = Post::leftJoin("users", "users.user_id", "post.user_id")
            ->leftJoin('upvote', function ($join) {
                $join->on('post.post_id', 'upvote.content_no')
                    ->where('upvote.content_type', 'post');
            })
            ->leftJoin('bookmark', 'post.post_id', 'bookmark.post_id')
            ->select("post.*", "users.username", "users.user_photo")
            ->selectRaw("COUNT(upvote.content_no) as upvotes")
            ->selectRaw("MAX(CASE WHEN upvote.user_id = ? THEN 1 ELSE 0 END) AS has_upvoted", [$uid])
            ->selectRaw("MAX(CASE WHEN bookmark.user_id = ? THEN 1 ELSE 0 END) AS has_bookmarked", [$uid])
            ->groupBy("post.post_id", "users.username", "users.user_photo")
            ->where('post.post_id', $id)
            ->first();

        return Inertia::render("Forums/Show", [
            "post_data" => $post,
            "comments_data" => Inertia::defer(fn() => $this->show_comments($id))
        ])->with('showFooter', false);
    }

    public function show_comments(string $id) {
        $uid = Auth::id();

        $comments = Comment::leftJoin("users", "users.user_id", "comment.user_id")
            ->leftJoin('upvote', function ($join) {
                $join->on('comment.comment_id', 'upvote.content_no')
                    ->where('upvote.content_type', 'comment');
            })
            ->select("comment.*", "users.username", "users.user_photo")
            ->selectRaw("COUNT(upvote.content_no) as upvotes")
            ->selectRaw("MAX(CASE WHEN upvote.user_id = ? THEN 1 ELSE 0 END) AS has_upvoted", [$uid])
            ->groupBy("comment.comment_id", "users.username", "users.user_photo")
            ->orderBy("comment.created_at", "asc")
            ->where('comment.post_id', $id)
            ->paginate(20);

        return $comments;
    }

    public function update_content(Request $request, string $type, string $id)
    {
        $validator = $request->validate([
            'description' => 'required|string'
        ],[
            'description.required' => 'Description field cannot be empty.'
        ]);

        if ($type == 'post') {
            Post::where('post_id', $id)->update(['description' => $validator['description']]);
        } else if ($type == 'comment') {
            Comment::where('comment_id', $id)->update(['description' => $validator['description']]);
        } else {
            return back()->with('error', 'Action invalid: Content type invalid / not specified');
        }

        return back();
    }

    public function destroy_content(string $type, string $id)
    {
        switch ($type) {
            case 'post':
                Post::destroy($id);
                return redirect('/forum')->with("success", "Post deleted successfully.");
            case 'comment':
                Comment::destroy($id);
                return back()->with("success", "Comment deleted successfully.");
            default:
                return back()->with('error', 'Action invalid: Content type invalid / specified');
        }
    }

    public function upvote_content(string $type, string $id)
    {
        if ($type !== 'post' && $type !== 'comment') {
            return back()->with('error', 'Action invalid: Content type invalid / not specified');
        }

        $upvote = Upvote::where("content_no", $id)
            ->where('content_type', $type)
            ->where("user_id", Auth::id())
            ->first();
        if ($upvote) {
            Upvote::where("content_no", $id)
                ->where('content_type', $type)
                ->where("user_id", Auth::id())
                ->delete();
        } else {
            Upvote::create([
                "content_no" => $id,
                "content_type" => $type,
                "user_id" => Auth::id(),
            ]);
        }
        return back();
    }

    public function bookmark(string $id)
    {
        $upvote = Bookmark::where("post_id", $id)
            ->where("user_id", Auth::id())
            ->first();
        if ($upvote) {
            Bookmark::where("post_id", $id)
                ->where("user_id", Auth::id())
                ->delete();
            $message = 'You have removed this post from your bookmark.';
        } else {
            Bookmark::create([
                "post_id" => $id,
                "user_id" => Auth::id(),
            ]);
            $message = 'You have added this post to your bookmark. Revisit them later in Bookmark.';
        }
        return back()->with('success', $message);
    }

    public function report_content(Request $request, string $type, string $id)
    {
        if ($type !== 'post' && $type !== 'comment') {
            return back()->with('error', 'Action invalid: Content type invalid / not specified');
        }

        // TODO: implement report store

        return back()->with('success', 'Content reported. Your report will be processed soon.');
    }
}
