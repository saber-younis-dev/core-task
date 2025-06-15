<?php

namespace App\Http\Controllers\API;

use App\Events\PostApproved;
use App\Events\PostSubmittedForApproval;
use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Gate;

class PostController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        if (Gate::denies('viewAny', Post::class)) {
            abort(403, 'Unauthorized action.');
        }

        // Users can see only published posts unless they have permission
        $query = Post::query()->with('user');

        if (!auth()->user()->hasPermission('view_all_posts')) {
            $query->where(function($q) {
                $q->where('status', 'published')
                    ->orWhere('user_id', auth()->id());
            });
        }

        $posts = $query->latest()->paginate(10);
        return PostResource::collection($posts);
    }

    public function store(Request $request)
    {
        if (Gate::denies('create', Post::class)) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'categories' => 'sometimes|array',
            'categories.*' => 'exists:categories,id',
        ]);

        // Create the post with pending status
        $post = $request->user()->posts()->create([
            'title' => $request->title,
            'content' => $request->content,
            'status' => Post::STATUS_PENDING, // Set to pending by default
        ]);

        // Attach categories if provided
        if ($request->has('categories')) {
            $post->categories()->attach($request->categories);
        }

        // Dispatch event for post submitted for approval
        event(new PostSubmittedForApproval($post));

        return new PostResource($post->load(['user', 'categories']));
    }

    public function show(Post $post)
    {
        if (Gate::denies('view', $post)) {
            abort(403, 'Unauthorized action.');
        }

        return new PostResource($post->load(['user', 'categories']));
    }

    public function update(Request $request, Post $post)
    {
        if (Gate::denies('update', $post)) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'title' => 'sometimes|string|max:255',
            'content' => 'sometimes|string',
            'status' => 'sometimes|string|in:draft,published',
            'categories' => 'sometimes|array',
            'categories.*' => 'exists:categories,id',
        ]);

        // Check if user is trying to publish
        if ($request->filled('status') && $request->status === 'published' && $post->status !== 'published') {
            if (Gate::denies('publish', $post)) {
                abort(403, 'Unauthorized to publish this post.');
            }
        }

        $post->update($request->only(['title', 'content', 'status']));

        // Sync categories if provided
        if ($request->has('categories')) {
            $post->categories()->sync($request->categories);
        }

        return new PostResource($post->load(['user', 'categories']));
    }

    public function destroy(Post $post)
    {
        if (Gate::denies('delete', $post)) {
            abort(403, 'Unauthorized action.');
        }

        $post->categories()->detach();
        $post->comments()->delete();
        $post->delete();

        return response()->json(['message' => 'Post deleted successfully']);
    }

    public function review(Request $request, Post $post)
    {
        if (!$request->user()->hasPermission('approve_posts')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'status' => 'required|in:published,rejected',
            'feedback' => 'nullable|string',
        ]);

        // Update the post status
        $post->update([
            'status' => $request->status
        ]);

        // Dispatch event for post approval
        event(new PostApproved($post, $request->user()));

        return new PostResource($post->load(['user', 'categories']));
    }

    public function pending(Request $request)
    {
        if (!$request->user()->hasPermission('approve_posts')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $posts = Post::pending()->with(['user', 'categories'])->latest()->paginate(10);

        return PostResource::collection($posts);
    }
}
