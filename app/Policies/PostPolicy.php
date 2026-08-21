<?php

namespace App\Policies;

use App\Models\Post;
use App\Models\User;

class PostPolicy
{
    /**
     * Authorize create: only users that pass the `manage-posts` gate.
     * The route middleware already enforces `manage-posts`; this method
     * covers direct `$user->can('create', Post::class)` calls.
     */
    public function create(User $user): bool
    {
        return $user->can('manage-posts');
    }

    /**
     * Authorize update: admin, or the post's own author — but only
     * if the user has a role that grants `manage-posts`. A user
     * without role is denied even on posts they authored.
     */
    public function update(User $user, Post $post): bool
    {
        return $user->can('manage-posts')
            && ($user->role === 'admin' || $post->author_id === $user->id);
    }

    /**
     * Authorize delete: admin, or the post's own author — gated by role.
     */
    public function delete(User $user, Post $post): bool
    {
        return $user->can('manage-posts')
            && ($user->role === 'admin' || $post->author_id === $user->id);
    }

    /**
     * Authorize restore: same criteria as delete.
     */
    public function restore(User $user, Post $post): bool
    {
        return $user->can('manage-posts')
            && ($user->role === 'admin' || $post->author_id === $user->id);
    }

    /**
     * Authorize force delete: admin only.
     */
    public function forceDelete(User $user, Post $post): bool
    {
        return $user->role === 'admin';
    }
}
