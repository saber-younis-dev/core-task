<?php

namespace App\Policies;

use App\Models\Post;
use App\Models\User;

class PostPolicy
{
    /**
     * Determine if the user can view any posts.
     */
    public function viewAny(User $user): bool
    {
        return true; // Anyone can view posts list
    }

    /**
     * Determine if the user can view the post.
     */
    public function view(User $user, Post $post): bool
    {
        // Published posts can be viewed by anyone
        if ($post->status === 'published') {
            return true;
        }

        // Users can only view their own draft posts
        return $user->id === $post->user_id || $user->hasPermission('view_all_posts');
    }

    /**
     * Determine if the user can create posts.
     */
    public function create(User $user): bool
    {
        return $user->hasPermission('create_posts');
    }

    /**
     * Determine if the user can update the post.
     */
    public function update(User $user, Post $post): bool
    {
        // Users can edit their own posts
        // Editors and admins can edit any post
        return $user->id === $post->user_id || $user->hasPermission('edit_others_posts');
    }

    /**
     * Determine if the user can delete the post.
     */
    public function delete(User $user, Post $post): bool
    {
        // Users can delete their own posts
        // Admins can delete any post
        return $user->id === $post->user_id || $user->hasPermission('delete_posts');
    }

    /**
     * Determine if the user can publish posts.
     */
    public function publish(User $user, Post $post): bool
    {
        // Only users with publish permission can publish posts
        return $user->hasPermission('publish_posts');
    }

    /**
     * Determine if the user can approve or reject posts.
     */
    public function approve(User $user): bool
    {
        return $user->hasPermission('approve_posts');
    }

    /**
     * Determine if the user can view pending posts.
     */
    public function viewPending(User $user): bool
    {
        return $user->hasPermission('approve_posts');
    }
}
