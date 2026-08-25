<?php

namespace App\Livewire\Users;

use App\Enums\UserRole;
use App\Filters\UserFilter;
use App\Livewire\Concerns\WithFilters;
use App\Models\User;
use Flux\Flux as FluxFacade;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.dashboard')]
#[Title('Users')]
class UsersIndex extends Component
{
    use WithFilters, WithPagination;

    #[Url(except: '')]
    public string $search = '';

    #[Url(except: '')]
    public string $role = '';

    public ?int $confirmingDeletion = null;

    public function mount(): void
    {
        $this->authorize('viewAny', User::class);
    }

    public function delete(int $userId): void
    {
        $user = User::query()->findOrFail($userId);

        Gate::authorize('delete', $user);

        $user->delete();
        $this->confirmingDeletion = null;

        FluxFacade::toast(variant: 'success', text: __('User deleted.'));
    }

    public function canUpdate(User $user): bool
    {
        return Gate::allows('update', $user);
    }

    public function canDelete(User $user): bool
    {
        return Gate::allows('delete', $user);
    }

    public function render(): View
    {
        $filter = new UserFilter($this->search, $this->role);

        return view('livewire.users.users-index', [
            'users' => $filter->apply()->paginate(15),
            'roles' => UserRole::cases(),
        ]);
    }
}
