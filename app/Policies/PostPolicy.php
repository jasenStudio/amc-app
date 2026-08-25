<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Post;
use App\Models\User;

class PostPolicy
{
    public function create(User $user): bool
    {
        return $user->can('manage-posts');
    }

    public function update(User $user, Post $post): bool
    {
        return $user->can('manage-posts')
            && (in_array($user->role, [UserRole::SuperAdmin, UserRole::Admin], true) || $post->author_id === $user->id);
    }

    public function delete(User $user, Post $post): bool
    {
        return $user->can('manage-posts')
            && (in_array($user->role, [UserRole::SuperAdmin, UserRole::Admin], true) || $post->author_id === $user->id);
    }

    public function restore(User $user, Post $post): bool
    {
        return $user->can('manage-posts')
            && (in_array($user->role, [UserRole::SuperAdmin, UserRole::Admin], true) || $post->author_id === $user->id);
    }

    public function forceDelete(User $user, Post $post): bool
    {
        return in_array($user->role, [UserRole::SuperAdmin, UserRole::Admin], true);
    }
}
