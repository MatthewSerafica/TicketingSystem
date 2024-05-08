<?php

namespace App\Http\Controllers;

use App\Http\Resources\PostResource;
use App\Models\Comment;
use App\Models\Post;
use App\Models\TaggedUser;
use App\Models\User;
use App\Models\Technician;
use App\Notifications\CommentMade;
use App\Notifications\PostMade;
use App\Notifications\ReplyMade;
use App\Notifications\UserTaggedComment;
use App\Notifications\UserTaggedPost;
use App\Notifications\UserTaggedReply;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TechnicianForumController extends Controller
{
    public function index(Request $request)
    {
        $auth = Auth::user();
        $posts = Post::query()
            ->whereNot('is_deleted', 1)
            ->with(['user', 'tagged' => function ($query) {
                $query->whereNull('comment_id');
            }, 'tagged.user'])
            ->withCount(['comment' => function ($query) {
                $query->where('is_deleted', '!=', 1);
            }])
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->input('search');
                $query->where('title', 'like', '%' . $search . '%')
                    ->orWhere('content', 'like', '%' . $search . '%')
                    ->orWhereHas('user', function ($subquery) use ($search) {
                        $subquery->where('name', 'like', '%' . $search . '%');
                    });
            })
            ->orderBy('created_at', 'desc')
            ->cursorPaginate(15);

        $posts->each(function ($post) {
            $post->time_since_posted = $post->created_at->diffForHumans();
        });

        if ($request->wantsJson()) {
            return PostResource::collection($posts);
        }
        $technicians = Technician::with('user')
            ->get();

        $filters = $request->only(['search']);
        if ($auth->user_type == 'technician') {
            return inertia('Technician/Forum/Index', [
                'posts' => PostResource::collection($posts),
                'filters' => $filters,
                'technicians' => $technicians,
            ]);
        } else if ($auth->user_type == 'admin' || $auth->user_type == 'super') {
            return inertia('Admin/Forum/Index', [
                'posts' => PostResource::collection($posts),
                'filters' => $filters,
                'technicians' => $technicians,
            ]);
        }
    }

    public function store(Request $request)
    {
        $auth = Auth::user();
        $user_id = $auth->id;
        $poster = User::findOrFail($user_id);
        $request->validate([
            'title' => 'nullable',
            'content' => 'required',
            'image' => 'nullable',
        ]);

        $post = new Post([
            'user_id' => $user_id,
            'title' => $request->title,
            'content' => $request->content,
        ]);

        if ($request->hasFile('image')) {
            $link = Storage::put('/posts', $request->file('image'));
            $post->image = $link;
        }

        $post->save();

        $tagged = json_decode($request->tagged_user);

        foreach ($tagged as $user) {
            $tagged = TaggedUser::create([
                'post_id' => $post->id,
                'user_id' => $user,
            ]);
            $user = User::findOrFail($user);
            $user->notify(
                new UserTaggedPost($user, $post)
            );
        }

        $taggedUserIds = json_decode($request->tagged_user, true);

        $techs = User::where(function ($query) use ($user_id, $taggedUserIds) {
            $query->where('user_type', 'technician')
                ->orWhere('user_type', 'admin')
                ->orWhere('user_type', 'super');
        })
            ->whereNotIn('id', array_merge([$user_id], $taggedUserIds))
            ->get();


        foreach ($techs as $tech) {
            $tech->notify(
                new PostMade($post, $poster->name)
            );
        }

        if ($auth->user_type == 'technician') {
            return redirect()->to(route('technician.forum'))->with('success', 'Post created successfully.');
        } else if ($auth->user_type == 'admin' || $auth->user_type == 'super') {
            return redirect()->to(route('admin.forum'))->with('success', 'Post created successfully.');
        }
    }

    public function show($id)
    {
        $auth = Auth::user();
        $post = Post::where('id', $id)
            ->with(['user', 'tagged' => function ($query) {
                $query->whereNull('comment_id');
            }, 'tagged.user'])
            ->withCount(['comment' => function ($query) {
                $query->where('is_deleted', '!=', 1);
            }])
            ->first();


        if ($post) {
            $post->time_since_posted = $post->created_at->diffForHumans();
        }

        $comments = Comment::where('post_id', $post->id)
            ->whereNot('is_deleted', 1)
            ->whereNull('parent_comment_id')
            ->with('user', 'tagged.user')
            ->orderBy('created_at', 'desc')
            ->get();


        $comments->each(function ($comment) {
            $comment->time_since_posted = $comment->created_at->diffForHumans();
        });

        $replies = Comment::where('post_id', $post->id)
            ->whereNot('is_deleted', 1)
            ->whereNotNull('parent_comment_id')
            ->with('user', 'tagged.user')
            ->orderBy('created_at', 'desc')
            ->get();

        $replies->each(function ($reply) {
            $reply->time_since_posted = $reply->created_at->diffForHumans();
        });


        if ($auth->user_type == 'technician') {
            return inertia('Technician/Forum/Show', [
                'post' => $post,
                'comments' => $comments,
                'replies' => $replies,
            ]);
        } else if ($auth->user_type == 'admin' || $auth->user_type == 'super') {
            return inertia('Admin/Forum/Show', [
                'post' => $post,
                'comments' => $comments,
                'replies' => $replies,
            ]);
        }
    }

    public function comment(Request $request, $id, $title)
    {
        try {
            $user_id = auth()->id();
            $commenter = User::findOrFail($user_id);
            $post = Post::findOrFail($id);

            $request->validate([
                'content' => 'required',
            ]);

            $comment = [
                'post_id' => $post->id,
                'user_id' => $user_id,
                'content' => $request->content,
            ];

            $comment = Comment::create($comment);

            $tagged = $request->tagged_user;

            foreach ($tagged as $user) {
                $tagged = TaggedUser::create([
                    'post_id' => $post->id,
                    'comment_id' => $comment->id,
                    'user_id' => $user,
                ]);

                $user = User::findOrFail($user);
                $user->notify(
                    new UserTaggedComment($user, $comment)
                );
            }

            if ($user_id !== $post->user_id) {
                $post->user->notify(
                    new CommentMade($comment, $commenter->name, $post->title)
                );
            }

            return redirect()->back()->with('success', 'Commented on the post!')->with('message', 'Comment successfully posted!');
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function reply(Request $request, $id, $title, $comment_id)
    {
        try {
            $user_id = auth()->id();
            $replier = User::findOrFail($user_id);
            $post = Post::findOrFail($id);

            $request->validate([
                'parent_comment_id' => 'nullable',
                'content' => 'required',
            ]);

            $comment = [
                'post_id' => $post->id,
                'parent_comment_id' => $comment_id,
                'user_id' => $user_id,
                'content' => $request->content,
            ];

            $reply = Comment::create($comment);

            $tagged = $request->tagged_user;

            foreach ($tagged as $user) {
                $tagged = TaggedUser::create([
                    'post_id' => $post->id,
                    'comment_id' => $reply->id,
                    'user_id' => $user,
                ]);

                $user = User::findOrFail($user);
                $user->notify(
                    new UserTaggedReply($user, $reply)
                );
            }

            $originalComment = Comment::with('user')->find($reply->parent_comment_id);

            if ($user_id !== $originalComment->user->id) {
                $originalComment->user->notify(new ReplyMade($reply, $replier->name, $post->title));
            }

            return redirect()->back()->with('success', 'Replied to the comment!')->with('message', 'Reply successfully posted!');
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $comment = Comment::findOrFail($id);

        $request->validate([
            'content' => 'required',
        ]);
        $comment->content = $request->content;
        $comment->save();

        $tagged = TaggedUser::where('comment_id', $id)->get();

        foreach ($tagged as $tag) {
            $tag->delete();
        }

        $tagged = $request->tagged_user;

        foreach ($tagged as $user) {
            $tagged = TaggedUser::create([
                'post_id' => $comment->post_id,
                'comment_id' => $comment->id,
                'user_id' => $user,
            ]);
        }

        return redirect()->back()->with('success', 'Comment successfully updated');
    }

    public function deleteComment($id)
    {
        $comment = Comment::findOrFail($id);
        $comment->is_deleted = 1;
        $comment->save();

        $children = Comment::where('parent_comment_id', $id)->get();

        if ($children) {
            foreach ($children as $child) {
                $child->is_deleted = 1;
                $child->save();
                $grandChildren = Comment::where('parent_comment_id', $child->id)->get();
                if ($grandChildren) {
                    foreach ($grandChildren as $grandChild) {
                        $grandChild->is_deleted = 1;
                        $grandChild->save();
                    }
                }
            }
        }

        return redirect()->back()->with('success', 'Comment successfully deleted');
    }

    public function delete($id)
    {
        $post = Post::findOrFail($id);
        $post->is_deleted = 1;
        $post->save();

        return redirect()->back()->with('success', 'Post Deleted!')->with('message', 'Your post has been deleted');
    }

    public function getUsers($term)
    {
        $auth = Auth::user();
        $users = User::where(function ($query) use ($term, $auth) {
            $query->where('user_type', 'technician')
                ->orWhere('user_type', 'admin')
                ->orWhere('user_type', 'super');
        })
            ->whereNot('id', $auth->id)
            ->where('name', 'like', '%' . $term . '%')
            ->get();

        return response()->json(['users' => $users]);
    }
}
