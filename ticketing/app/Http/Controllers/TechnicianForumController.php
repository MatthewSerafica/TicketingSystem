<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use Exception;
use Illuminate\Http\Request;

class TechnicianForumController extends Controller
{
    public function index(Request $request)
    {
        $posts = Post::query()
            ->with('user')
            ->withCount('comment')
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->input('search');
                $query->where('title', 'like', '%' . $search . '%')
                    ->orWhere('content', 'like', '%' . $search . '%');
            })
            ->orderBy('created_at', 'desc')
            ->get();

        $posts->each(function ($post) {
            $post->time_since_posted = $post->created_at->diffForHumans();
        });

        $filters = $request->only(['search']);
        return inertia('Technician/Forum/Index', [
            'posts' => $posts,
            'filters' => $filters,
        ]);
    }

    public function store(Request $request)
    {
        $user_id = auth()->id();
        $request->validate([
            'title' => 'nullable',
            'content' => 'required',
            'tagged_user' => 'nullable',
        ]);

        $post = [
            'user_id' => $user_id,
            'title' => $request->title,
            'content' => $request->content,
            'tagged_user' => $request->tagged_user,
        ];

        Post::create($post);

        return redirect()->back()->with('success', 'Posted!')->with('message', 'Message posted successfully!');
    }

    public function show($id)
    {
        $post = Post::where('id', $id)
            ->with('user')
            ->withCount('comment')
            ->first();
            
        if ($post) {
            $post->time_since_posted = $post->created_at->diffForHumans();
        }

        $comments = Comment::where('post_id', $post->id)
            ->whereNull('parent_comment_id')
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();


        $comments->each(function ($comment) {
            $comment->time_since_posted = $comment->created_at->diffForHumans();
        });

        $replies = Comment::where('post_id', $post->id)
            ->whereNotNull('parent_comment_id')
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        $replies->each(function ($reply) {
            $reply->time_since_posted = $reply->created_at->diffForHumans();
        });

        return inertia('Technician/Forum/Show', [
            'post' => $post,
            'comments' => $comments,
            'replies' => $replies,
        ]);
    }

    public function comment(Request $request, $id, $title)
    {
        try {
            $user_id = auth()->id();
            $post = Post::where('id', $id)->first();

            $request->validate([
                'content' => 'required',
                'tagged_user' => 'nullable',
            ]);

            $comment = [
                'post_id' => $post->id,
                'user_id' => $user_id,
                'content' => $request->content,
                'tagged_user' => $request->tagged_user,
            ];

            Comment::create($comment);

            return redirect()->back()->with('success', 'Commented on the post!')->with('message', 'Comment successfully posted!');
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function reply(Request $request, $id, $title, $comment_id)
    {
        try {
            $user_id = auth()->id();
            $post = Post::where('id', $id)->first();

            $request->validate([
                'parent_comment_id' => 'nullable',
                'content' => 'required',
                'tagged_user' => 'nullable',
            ]);

            $comment = [
                'post_id' => $post->id,
                'parent_comment_id' => $comment_id,
                'user_id' => $user_id,
                'content' => $request->content,
                'tagged_user' => $request->tagged_user,
            ];

            Comment::create($comment);

            return redirect()->back()->with('success', 'Commented on the post!')->with('message', 'Comment successfully posted!');
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
