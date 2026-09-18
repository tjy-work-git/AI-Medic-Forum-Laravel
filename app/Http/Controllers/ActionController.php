<?php

namespace App\Http\Controllers;

use App\Models\Bookmark;
use App\Models\Upvote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

// This controller is make for other subactions (upvote, bookmark)
class ActionController extends Controller
{
    public function upvote_post(string $id)
    {
        $upvote = Upvote::where("content_no", $id)
            ->where('content_type', 'post')
            ->where("user_id", Auth::id())
            ->first();
        if ($upvote) {
            Upvote::where("content_no", $id)
                ->where('content_type', 'post')
                ->where("user_id", Auth::id())
                ->delete();
        } else {
            Upvote::create([
                "content_no" => $id,
                "content_type" => 'post',
                "user_id" => Auth::id(),
            ]);
        }
        return back();
    }

    public function upvote_comment(string $id)
    {
        $upvote = Upvote::where("content_no", $id)
            ->where('content_type', 'comment')
            ->where("user_id", Auth::id())
            ->first();
        if ($upvote) {
            Upvote::where("content_no", $id)
                ->where('content_type', 'comment')
                ->where("user_id", Auth::id())
                ->delete();
        } else {
            Upvote::create([
                "content_no" => $id,
                "content_type" => 'comment',
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
}
