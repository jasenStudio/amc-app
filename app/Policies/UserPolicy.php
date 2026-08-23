<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, [UserRole::SuperAdmin, UserRole::Admin], true);
    }

    public function view(User $user, User $target): bool
    {
        return in_array($user->role, [UserRole::SuperAdmin, UserRole::Admin], true);
    }

    public function create(User $user): bool
    {
        return in_array($user->role, [UserRole::SuperAdmin, UserRole::Admin], true);
    }

    public function update(User $user, User $target): bool
    {
        if ($user->is($target)) {
            return false;
        }

        return in_array($target->role, $user->role->manages(), true);
    }

    public function delete(User $user, User $target): bool
    {
        if ($user->is($target)) {
            return false;
        }

        return in_array($target->role, $user->role->manages(), true);
    }

    public function changeRole(User $user, User $target, UserRole $newRole): bool
    {
        if ($user->is($target)) {
            return false;
        }

        $manages = $user->role->manages();

        return in_array($target->role, $manages, true)
            && in_array($newRole, $manages, true);
    }
}
