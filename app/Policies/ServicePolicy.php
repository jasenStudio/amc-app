<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Service;
use App\Models\User;

class ServicePolicy
{
    public function viewAny(User $user): bool
    {
        return $this->canManage($user);
    }

    public function view(User $user, Service $service): bool
    {
        return $this->canManage($user);
    }

    public function create(User $user): bool
    {
        return $this->canManage($user);
    }

    public function update(User $user, Service $service): bool
    {
        return $this->canManage($user);
    }

    public function delete(User $user, Service $service): bool
    {
        return $this->canManage($user);
    }

    public function restore(User $user, Service $service): bool
    {
        return $this->canManage($user);
    }

    public function forceDelete(User $user, Service $service): bool
    {
        return in_array($user->role, [UserRole::SuperAdmin, UserRole::Admin], true);
    }

    private function canManage(User $user): bool
    {
        return in_array($user->role, [UserRole::SuperAdmin, UserRole::Admin], true);
    }
}
