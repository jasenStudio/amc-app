<?php

namespace Tests\Feature\Users;

use App\Enums\UserRole;
use App\Livewire\Users\UsersIndex;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class UsersIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get(route('dashboard.users.index'))
            ->assertRedirect(route('login'));
    }

    public function test_pending_user_is_redirected_to_pending_approval(): void
    {
        $pending = User::factory()->pending()->create();

        $this->actingAs($pending)
            ->get(route('dashboard.users.index'))
            ->assertRedirect(route('pending.approval'));
    }

    public function test_guest_cannot_access_pending_approval_page(): void
    {
        $this->get(route('pending.approval'))
            ->assertRedirect(route('login'));
    }

    public function test_pending_user_can_logout_from_pending_approval_page(): void
    {
        $pending = User::factory()->pending()->create();

        $this->actingAs($pending)
            ->post(route('logout'))
            ->assertRedirect('/');

        $this->assertGuest();
    }

    public function test_regular_roles_never_redirected_to_pending_approval(): void
    {
        foreach ([
            User::factory()->superAdmin()->create(),
            User::factory()->admin()->create(),
            User::factory()->editor()->create(),
        ] as $user) {
            $this->actingAs($user)
                ->get(route('dashboard'))
                ->assertOk();
        }
    }

    public function test_editor_is_forbidden(): void
    {
        $editor = User::factory()->editor()->create();

        $this->actingAs($editor)
            ->get(route('dashboard.users.index'))
            ->assertForbidden();
    }

    public function test_admin_can_view_index(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('dashboard.users.index'))
            ->assertOk();
    }

    public function test_super_admin_can_view_index(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();

        $this->actingAs($superAdmin)
            ->get(route('dashboard.users.index'))
            ->assertOk();
    }

    public function test_admin_sees_all_users(): void
    {
        $admin = User::factory()->admin()->create();
        User::factory()->superAdmin()->create();
        User::factory()->editor()->create();
        User::factory()->pending()->create();

        Livewire::actingAs($admin)
            ->test(UsersIndex::class)
            ->assertViewHas('users', fn ($users) => $users->total() === 4);
    }

    public function test_search_filters_by_name(): void
    {
        $admin = User::factory()->admin()->create();
        User::factory()->create(['name' => 'John Doe']);
        User::factory()->create(['name' => 'Jane Smith']);

        Livewire::actingAs($admin)
            ->test(UsersIndex::class)
            ->set('search', 'John')
            ->assertViewHas('users', fn ($users) => $users->total() === 1);
    }

    public function test_role_filter(): void
    {
        $admin = User::factory()->admin()->create();
        User::factory()->editor()->create();
        User::factory()->pending()->create();

        Livewire::actingAs($admin)
            ->test(UsersIndex::class)
            ->set('role', UserRole::Editor->value)
            ->assertViewHas('users', fn ($users) => $users->total() === 1);
    }

    public function test_admin_cannot_see_delete_button_for_self(): void
    {
        $admin = User::factory()->admin()->create();

        Livewire::actingAs($admin)
            ->test(UsersIndex::class)
            ->assertDontSeeHtml("data-test=\"delete-user-{$admin->id}\"");
    }

    public function test_admin_cannot_see_edit_button_for_self(): void
    {
        $admin = User::factory()->admin()->create();

        Livewire::actingAs($admin)
            ->test(UsersIndex::class)
            ->assertDontSeeHtml("data-test=\"edit-user-{$admin->id}\"");
    }

    public function test_admin_cannot_see_delete_button_for_other_admin(): void
    {
        $admin = User::factory()->admin()->create();
        $otherAdmin = User::factory()->admin()->create(['name' => 'Other Admin']);

        Livewire::actingAs($admin)
            ->test(UsersIndex::class)
            ->assertSee('Other Admin')
            ->assertDontSeeHtml("data-test=\"delete-user-{$otherAdmin->id}\"");
    }

    public function test_admin_can_see_edit_button_for_editor(): void
    {
        $admin = User::factory()->admin()->create();
        $editor = User::factory()->editor()->create(['name' => 'Editor User']);

        Livewire::actingAs($admin)
            ->test(UsersIndex::class)
            ->assertSee('Editor User')
            ->assertSeeHtml("data-test=\"edit-user-{$editor->id}\"")
            ->assertSeeHtml("data-test=\"delete-user-{$editor->id}\"");
    }

    public function test_super_admin_can_see_buttons_for_admin(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();
        $admin = User::factory()->admin()->create(['name' => 'Admin User']);

        Livewire::actingAs($superAdmin)
            ->test(UsersIndex::class)
            ->assertSee('Admin User')
            ->assertSeeHtml("data-test=\"edit-user-{$admin->id}\"")
            ->assertSeeHtml("data-test=\"delete-user-{$admin->id}\"");
    }

    public function test_admin_can_delete_editor(): void
    {
        $admin = User::factory()->admin()->create();
        $editor = User::factory()->editor()->create();

        Livewire::actingAs($admin)
            ->test(UsersIndex::class)
            ->call('delete', $editor->id);

        $this->assertSoftDeleted('users', ['id' => $editor->id]);
    }

    public function test_soft_deleted_user_does_not_appear_in_listing(): void
    {
        $admin = User::factory()->admin()->create();
        $editor = User::factory()->editor()->create(['name' => 'Visible Editor']);
        $softDeleted = User::factory()->editor()->create(['name' => 'Deleted Editor']);
        $softDeleted->delete();

        Livewire::actingAs($admin)
            ->test(UsersIndex::class)
            ->assertSee('Visible Editor')
            ->assertDontSee('Deleted Editor')
            ->assertViewHas('users', fn ($users) => $users->total() === 2);
    }

    public function test_soft_deleted_user_cannot_login(): void
    {
        $user = User::factory()->editor()->create([
            'email' => 'deleted@example.com',
            'password' => 'password',
        ]);
        $user->delete();

        $this->post(route('login'), [
            'email' => 'deleted@example.com',
            'password' => 'password',
        ]);

        $this->assertGuest();
    }

    public function test_admin_cannot_delete_other_admin(): void
    {
        $admin = User::factory()->admin()->create();
        $otherAdmin = User::factory()->admin()->create();

        Livewire::actingAs($admin)
            ->test(UsersIndex::class)
            ->call('delete', $otherAdmin->id);

        $this->assertNotNull(User::find($otherAdmin->id));
    }
}
