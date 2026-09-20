<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use DOMDocument;
use Exception;
use Illuminate\Http\Request;
use OpenAI;

class OpenAIController extends Controller
{
    public function summarize(Request $request, string $id)
    {
        if (!$id) {
            return back()->with('errors', 'Action invalid');
        }
        $data = $this->data_query($id);

        // prepare openai
        try {
            $client = OpenAI::client(env('OPENAI_API_KEY'));
        } catch (Exception $e) {
            return back()->with('errors', 'Summarization feature temporarily unavailable. Please try again soon');
        }

        $prompt = match ($request->mode) {
            'abstract' => "Summarize these abstractively (in general concept):",
            'extract' => "Summarize these extractively (extract main point):",
            'general' => "Summarize these in general : ",
            default => "Summarize:"
        };

        $input = "$prompt\n{$data['data']}\n{$data['ext_content']}";

        $response = $client->chat()->create([
            'model' => 'gpt-4o',
            'messages' => [
                ['role' => 'system', 'content' => 'You are a helpful summary assistant for WeDoCare forum about medical topics. '
                    . 'Assume the user are medical patients, provide them the necessary information summary based on the input.'
                    . 'Format your answers using HTML (e.g., <b>, <i>, <ul>, <li>, <p>) where appropriate. '
                    . 'Return "Summary are not available at the moment." if the topics are unrelated to medical topics,'
                    . 'or not enough content to generate a helpful summary with answers.'
                    . 'Perform cross reference between online source and the discussion to validate the information.'
                    . 'If any web links is provided, include them at the end of the summary as a reference.'
                    . 'Encourage users to read the web link if summarize on the link cannot be done.'
                    . 'Notify users if inconsistency is spotted.'],
                ['role' => 'user', 'content' => $input]
            ],
            'temperature' => 0.5,
            'max_tokens' => 1000,
        ]);

        // change soon
        return $response['choices'][0]['message']['content'];
    }

    private function data_query(string $id)
    {
        try {
            $post = Post::leftJoin("users", "users.user_id", "post.user_id")
                ->select("post.*", "users.username")
                ->groupBy("post.post_id", "users.username")
                ->where('post.post_id', $id)
                ->first();
        } catch (Exception $e) {
            return abort(500);
        }

        if (!$post) {
            return abort(404);
        }

        try {
            $comments = Comment::leftJoin("users", "users.user_id", "comment.user_id")
                ->select("comment.*", "users.username")
                ->groupBy("comment.comment_id", "users.username")
                ->orderBy("comment.created_at", "asc")
                ->where('comment.post_id', $id)
                ->paginate(20);
        } catch (Exception $e) {
            return abort(500);
        }

        return $this->input_format($post, $comments);
    }

    private function input_format($post, $comments)
    {
        // restructure it in a readable way
        $post_restruct = "{$post->username} ({$post->created_at}):<br> {$post->description}";

        // restructure for each comment too
        $comment_restruct = "";
        if ($comments->count() > 0) {
            foreach ($comments as $comment) {
                $comment_restruct .= "{$comment->username} ({$comment->created_at}):<br> {$comment->description}";
            }
        }

        $data = $post_restruct . "\n\n" . $comment_restruct;

        // Extract links from prepared summary data
        preg_match_all('/https?:\/\/\S+/', $data, $matches); // Runs through restructured post and comments together
        $links = $matches[0] ?? [];

        // Used to clean each article
        $ext_content = "";
        foreach ($links as $link) {
            $content = $this->clean_article($link);
            if ($content) {
                $ext_content .= "\n\nReferenced Article Content from $link:\n" . $content;
            }
        }

        return [
            'data' => $data,
            'ext_content' => $ext_content
        ];
    }

    private function clean_article($link) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $link);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0');
        curl_setopt($ch, CURLOPT_REFERER, "https://www.google.com/");
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
            'Accept-Language: en-US,en;q=0.9',
            'Connection: keep-alive',
        ]);

        $html = curl_exec($ch);
        curl_close($ch);

        if (!$html)
            return null;

        libxml_use_internal_errors(true);
        $doc = new DOMDocument();
        $doc->loadHTML($html);
        libxml_clear_errors();

        $tagsToTry = ['article', 'main', 'body'];
        $textContent = '';

        foreach ($tagsToTry as $tag) {
            $elements = $doc->getElementsByTagName($tag);
            if ($elements->length > 0) {
                $textContent = $elements->item(0)->textContent;
                break;
            }
        }

        $textContent = preg_replace('/\s+/', ' ', $textContent);
        return trim($textContent);
    }
}