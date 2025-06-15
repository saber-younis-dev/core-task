<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\CommentResource;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function store(Request $request, Post $post)
    {
        $request->validate([
            'content' => 'required|string',
        ]);

        $comment = $post->comments()->create([
            'content' => $request->content,
            'user_id' => auth()->id(),
            'is_approved' => auth()->user()->hasRole(['admin', 'editor']) ? true : false,
        ]);

        return new CommentResource($comment->load(['user']));
    }

    public function update(Request $request, Comment $comment)
    {
        // Check if user owns the comment or is admin/editor
        if ($comment->user_id !== auth()->id() && !auth()->user()->hasRole(['admin', 'editor'])) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'content' => 'required|string',
        ]);

        $comment->update([
            'content' => $request->content,
            // If edited by normal user and was already approved, require re-approval
            'is_approved' => (!auth()->user()->hasRole(['admin', 'editor']) && $comment->is_approved) ? false : $comment->is_approved,
        ]);

        return new CommentResource($comment->load(['user']));
    }

    public function destroy(Comment $comment)
    {
        // Check if user owns the comment or is admin/editor
        if ($comment->user_id !== auth()->id() && !auth()->user()->hasRole(['admin', 'editor'])) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $comment->delete();

        return response()->json(['message' => 'Comment deleted successfully']);
    }

    // Admin/Editor only endpoint to approve comments
    public function approve(Comment $comment)
    {
        if (!auth()->user()->hasRole(['admin', 'editor'])) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $comment->update(['is_approved' => true]);

        return new CommentResource($comment->load(['user']));
    }
}
