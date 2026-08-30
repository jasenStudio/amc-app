<?php

namespace App\Livewire\Users;

use App\Actions\Users\SaveUser;
use App\Enums\UserRole;
use App\Models\User;
use Flux\Flux as FluxFacade;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('layouts.dashboard')]
class UserForm extends Component
{
    public ?User $user = null;

    #[Validate]
    public string $name = '';

    #[Validate]
    public string $email = '';

    #[Validate]
    public string $role = 'pending';

    #[Validate]
    public string $password = '';

    public function mount(int $userId = 0): void
    {
        if ($userId === 0) {
            $routeUser = request()->route('user');
            if ($routeUser !== null) {
                $userId = is_object($routeUser) && method_exists($routeUser, 'getKey')
                    ? (int) $routeUser->getKey()
                    : (int) $routeUser;
            }
        }

        if ($userId > 0) {
            $this->user = User::query()->findOrFail($userId);

            $actor = Auth::user();
            if ($actor !== null && ! $actor->is($this->user)) {
                $this->authorize('update', $this->user);
            }

            $this->name = $this->user->name;
            $this->email = $this->user->email;
            $this->role = $this->user->role->value;
        } else {
            $this->authorize('create', User::class);
        }
    }

    public function save(): void
    {
        $validated = $this->validate($this->rules());

        $actor = Auth::user();
        $isSelf = $actor !== null && $this->user !== null && $actor->is($this->user);

        if ($isSelf) {
            $validated['role'] = $this->user->role->value;
        } else {
            $newRole = UserRole::from($validated['role']);

            if ($this->user !== null && $this->user->role !== $newRole) {
                Gate::authorize('changeRole', [$this->user, $newRole]);
            }
        }

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
        ];

        if (! empty($validated['password'])) {
            $data['password'] = $validated['password'];
        }

        app(SaveUser::class)->handle($this->user, $data);

        FluxFacade::toast(variant: 'success', text: __('User saved.'));

        $this->redirectRoute('dashboard.users.index', navigate: true);
    }

    public function render(): View
    {
        $actor = Auth::user();
        $availableRoles = $actor instanceof User
            ? $actor->role->manages()
            : [];

        if ($this->user !== null && ! in_array($this->user->role, $availableRoles, true)) {
            $availableRoles = [$this->user->role];
        }

        return view('livewire.users.user-form', [
            'availableRoles' => $availableRoles,
            'isSelf' => $this->user !== null && Auth::user()?->is($this->user),
        ])->title($this->user ? __('Edit user') : __('New user'));
    }

    /**
     * @return array<string, mixed>
     */
    protected function rules(): array
    {
        $emailRules = ['required', 'email', 'max:255'];
        if ($this->user !== null) {
            $emailRules[] = Rule::unique('users', 'email')->ignore($this->user->id);
        } else {
            // TODO: detect when the email belongs to a soft-deleted user and offer
            // account reactivation instead of blocking with a generic "already taken" error.
            $emailRules[] = Rule::unique('users', 'email');
        }

        $passwordRules = $this->user !== null
            ? ['nullable', 'string', 'min:8']
            : ['required', 'string', 'min:8'];

        $actor = Auth::user();
        $isSelf = $actor !== null && $this->user !== null && $actor->is($this->user);

        $roleRules = ['required', 'string'];
        if ($isSelf) {
            $roleRules[] = Rule::in(array_map(fn (UserRole $r) => $r->value, UserRole::cases()));
        } else {
            $allowedRoles = [];
            if ($actor instanceof User) {
                $allowedRoles = array_map(fn (UserRole $r) => $r->value, $actor->role->manages());
            }
            if (! empty($allowedRoles)) {
                $roleRules[] = Rule::in($allowedRoles);
            } else {
                $roleRules[] = Rule::in(array_map(fn (UserRole $r) => $r->value, UserRole::cases()));
            }
        }

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => $emailRules,
            'role' => $roleRules,
            'password' => $passwordRules,
        ];
    }
}
