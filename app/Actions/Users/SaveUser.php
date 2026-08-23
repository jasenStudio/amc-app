<?php

namespace App\Actions\Users;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SaveUser
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function handle(?User $user, array $data): User
    {
        return DB::transaction(function () use ($user, $data): User {
            if (isset($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            }

            if ($user !== null) {
                if (isset($data['password'])) {
                    $user->update($data);
                } else {
                    unset($data['password']);
                    $user->update($data);
                }
            } else {
                if (! isset($data['password'])) {
                    $data['password'] = Hash::make('password');
                }
                $user = User::create($data);
            }

            return $user;
        });
    }
}
