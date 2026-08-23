<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Project;
use App\Models\User;

class ProjectPolicy
{
    public function viewAny(User $user): bool
    {
        return $this->canManage($user);
    }

    public function view(User $user, Project $project): bool
    {
        return $this->canManage($user);
    }

    public function create(User $user): bool
    {
        return $this->canManage($user);
    }

    public function update(User $user, Project $project): bool
    {
        return $this->canManage($user);
    }

    public function delete(User $user, Project $project): bool
    {
        return $this->canManage($user);
    }

    public function restore(User $user, Project $project): bool
    {
        return $this->canManage($user);
    }

    public function forceDelete(User $user, Project $project): bool
    {
        return in_array($user->role, [UserRole::SuperAdmin, UserRole::Admin], true);
    }

    private function canManage(User $user): bool
    {
        return in_array($user->role, [UserRole::SuperAdmin, UserRole::Admin], true);
    }
}
