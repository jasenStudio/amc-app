<?php

namespace Tests\Feature\Users;

use App\Enums\UserRole;
use App\Livewire\Users\UserForm;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class UserFormTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected(): void
    {
        $this->get(route('dashboard.users.create'))->assertRedirect(route('login'));
        $this->get(route('dashboard.users.edit', ['user' => 1]))->assertRedirect(route('login'));
    }

    public function test_pending_user_is_redirected_on_create(): void
    {
        $pending = User::factory()->pending()->create();

        $this->actingAs($pending)
            ->get(route('dashboard.users.create'))
            ->assertRedirect(route('pending.approval'));
    }

    public function test_editor_is_forbidden_on_create(): void
    {
        $editor = User::factory()->editor()->create();

        $this->actingAs($editor)
            ->get(route('dashboard.users.create'))
            ->assertForbidden();
    }

    public function test_admin_can_open_create_form(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('dashboard.users.create'))
            ->assertOk();
    }

    public function test_admin_can_create_user(): void
    {
        $admin = User::factory()->admin()->create();

        Livewire::actingAs($admin)
            ->test(UserForm::class)
            ->set('name', 'New User')
            ->set('email', 'newuser@example.com')
            ->set('role', UserRole::Editor->value)
            ->set('password', 'secret123')
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('dashboard.users.index'));

        $this->assertDatabaseHas('users', [
            'name' => 'New User',
            'email' => 'newuser@example.com',
            'role' => UserRole::Editor->value,
        ]);
    }

    public function test_admin_can_open_edit_form_for_editor(): void
    {
        $admin = User::factory()->admin()->create();
        $editor = User::factory()->editor()->create();

        $this->actingAs($admin)
            ->get(route('dashboard.users.edit', ['user' => $editor->id]))
            ->assertOk();
    }

    public function test_admin_cannot_open_edit_form_for_other_admin(): void
    {
        $admin = User::factory()->admin()->create();
        $otherAdmin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('dashboard.users.edit', ['user' => $otherAdmin->id]))
            ->assertForbidden();
    }

    public function test_super_admin_can_open_edit_form_for_admin(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();
        $admin = User::factory()->admin()->create();

        $this->actingAs($superAdmin)
            ->get(route('dashboard.users.edit', ['user' => $admin->id]))
            ->assertOk();
    }

    public function test_admin_can_update_editor(): void
    {
        $admin = User::factory()->admin()->create();
        $editor = User::factory()->editor()->create(['name' => 'Old Name']);

        Livewire::actingAs($admin)
            ->test(UserForm::class, ['userId' => $editor->id])
            ->set('name', 'New Name')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertSame('New Name', $editor->fresh()->name);
    }

    public function test_admin_can_change_editor_role_to_pending(): void
    {
        $admin = User::factory()->admin()->create();
        $editor = User::factory()->editor()->create();

        Livewire::actingAs($admin)
            ->test(UserForm::class, ['userId' => $editor->id])
            ->set('role', UserRole::Pending->value)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertSame(UserRole::Pending, $editor->fresh()->role);
    }

    public function test_admin_cannot_change_editor_role_to_super_admin(): void
    {
        $admin = User::factory()->admin()->create();
        $editor = User::factory()->editor()->create();

        Livewire::actingAs($admin)
            ->test(UserForm::class, ['userId' => $editor->id])
            ->set('role', UserRole::SuperAdmin->value)
            ->call('save')
            ->assertHasErrors('role');
    }

    public function test_self_role_field_is_enforced_on_backend(): void
    {
        $admin = User::factory()->admin()->create();

        Livewire::actingAs($admin)
            ->test(UserForm::class, ['userId' => $admin->id])
            ->set('role', UserRole::Editor->value)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertSame(UserRole::Admin, $admin->fresh()->role);
    }

    public function test_required_fields_are_validated(): void
    {
        $admin = User::factory()->admin()->create();

        Livewire::actingAs($admin)
            ->test(UserForm::class)
            ->set('name', '')
            ->set('email', '')
            ->call('save')
            ->assertHasErrors(['name', 'email']);
    }

    public function test_email_must_be_unique(): void
    {
        $admin = User::factory()->admin()->create();
        User::factory()->create(['email' => 'taken@example.com']);

        Livewire::actingAs($admin)
            ->test(UserForm::class)
            ->set('name', 'Test')
            ->set('email', 'taken@example.com')
            ->set('role', UserRole::Editor->value)
            ->set('password', 'secret123')
            ->call('save')
            ->assertHasErrors('email');
    }

    public function test_email_must_be_unique_even_for_soft_deleted_users(): void
    {
        $admin = User::factory()->admin()->create();
        $deleted = User::factory()->create(['email' => 'deleted@example.com']);
        $deleted->delete();

        Livewire::actingAs($admin)
            ->test(UserForm::class)
            ->set('name', 'Test')
            ->set('email', 'deleted@example.com')
            ->set('role', UserRole::Editor->value)
            ->set('password', 'secret123')
            ->call('save')
            ->assertHasErrors('email');
    }

    public function test_email_unique_validation_excludes_self_on_edit(): void
    {
        $admin = User::factory()->admin()->create();
        $editor = User::factory()->editor()->create(['email' => 'editor@example.com']);

        Livewire::actingAs($admin)
            ->test(UserForm::class, ['userId' => $editor->id])
            ->set('email', 'editor@example.com')
            ->call('save')
            ->assertHasNoErrors();
    }

    public function test_password_is_required_on_create(): void
    {
        $admin = User::factory()->admin()->create();

        Livewire::actingAs($admin)
            ->test(UserForm::class)
            ->set('name', 'Test')
            ->set('email', 'test@example.com')
            ->set('role', UserRole::Editor->value)
            ->set('password', '')
            ->call('save')
            ->assertHasErrors('password');
    }

    public function test_password_is_optional_on_edit(): void
    {
        $admin = User::factory()->admin()->create();
        $editor = User::factory()->editor()->create(['name' => 'Old Name']);

        Livewire::actingAs($admin)
            ->test(UserForm::class, ['userId' => $editor->id])
            ->set('name', 'New Name')
            ->set('password', '')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertSame('New Name', $editor->fresh()->name);
    }

    public function test_password_can_be_changed_on_edit(): void
    {
        $admin = User::factory()->admin()->create();
        $editor = User::factory()->editor()->create();
        $originalPassword = $editor->password;

        Livewire::actingAs($admin)
            ->test(UserForm::class, ['userId' => $editor->id])
            ->set('password', 'newpassword123')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertNotSame($originalPassword, $editor->fresh()->password);
    }

    public function test_password_is_hashed_on_create(): void
    {
        $admin = User::factory()->admin()->create();

        Livewire::actingAs($admin)
            ->test(UserForm::class)
            ->set('name', 'Test')
            ->set('email', 'test@example.com')
            ->set('role', UserRole::Editor->value)
            ->set('password', 'secret123')
            ->call('save')
            ->assertHasNoErrors();

        $user = User::where('email', 'test@example.com')->first();
        $this->assertNotSame('secret123', $user->password);
        $this->assertTrue(password_verify('secret123', $user->password));
    }
}
