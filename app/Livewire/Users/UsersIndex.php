<?php

namespace App\Livewire\Users;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.dashboard')]
#[Title('Users')]
class UsersIndex extends Component
{
    public function render(): View
    {
        Gate::authorize('admin');

        return view('livewire.users.users-index', [
            'users' => User::query()->orderByDesc('id')->paginate(15),
        ]);
    }
}
