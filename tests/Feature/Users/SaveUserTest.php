<?php

namespace Tests\Feature\Users;

use App\Actions\Users\SaveUser;
use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SaveUserTest extends TestCase
{
    use RefreshDatabase;

    private SaveUser $action;

    protected function setUp(): void
    {
        parent::setUp();
        $this->action = new SaveUser;
    }

    public function test_creates_new_user(): void
    {
        $user = $this->action->handle(null, [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'role' => UserRole::Editor->value,
        ]);

        $this->assertNotNull($user->id);
        $this->assertSame('John Doe', $user->name);
        $this->assertSame('john@example.com', $user->email);
        $this->assertSame(UserRole::Editor, $user->role);
    }

    public function test_updates_existing_user(): void
    {
        $user = User::factory()->create(['name' => 'Old Name']);

        $result = $this->action->handle($user, [
            'name' => 'New Name',
            'email' => $user->email,
            'role' => $user->role->value,
        ]);

        $this->assertSame('New Name', $result->fresh()->name);
    }

    public function test_can_change_user_role(): void
    {
        $user = User::factory()->editor()->create();

        $result = $this->action->handle($user, [
            'name' => $user->name,
            'email' => $user->email,
            'role' => UserRole::Admin->value,
        ]);

        $this->assertSame(UserRole::Admin, $result->fresh()->role);
    }

    public function test_creates_user_with_pending_role(): void
    {
        $user = $this->action->handle(null, [
            'name' => 'Pending User',
            'email' => 'pending@example.com',
            'role' => UserRole::Pending->value,
        ]);

        $this->assertSame(UserRole::Pending, $user->role);
    }

    public function test_creates_user_with_super_admin_role(): void
    {
        $user = $this->action->handle(null, [
            'name' => 'Super Admin',
            'email' => 'super@example.com',
            'role' => UserRole::SuperAdmin->value,
        ]);

        $this->assertSame(UserRole::SuperAdmin, $user->role);
    }

    public function test_creates_user_with_explicit_password(): void
    {
        $user = $this->action->handle(null, [
            'name' => 'Test',
            'email' => 'test@example.com',
            'role' => UserRole::Editor->value,
            'password' => 'mypassword',
        ]);

        $this->assertNotSame('mypassword', $user->password);
        $this->assertTrue(password_verify('mypassword', $user->password));
    }

    public function test_creates_user_with_default_password_when_not_provided(): void
    {
        $user = $this->action->handle(null, [
            'name' => 'Test',
            'email' => 'test@example.com',
            'role' => UserRole::Editor->value,
        ]);

        $this->assertTrue(password_verify('password', $user->password));
    }

    public function test_updates_password_when_provided(): void
    {
        $user = User::factory()->editor()->create();
        $originalPassword = $user->password;

        $this->action->handle($user, [
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role->value,
            'password' => 'newpassword',
        ]);

        $this->assertNotSame($originalPassword, $user->fresh()->password);
        $this->assertTrue(password_verify('newpassword', $user->fresh()->password));
    }

    public function test_does_not_change_password_when_not_provided_on_update(): void
    {
        $user = User::factory()->editor()->create();
        $originalPassword = $user->password;

        $this->action->handle($user, [
            'name' => 'New Name',
            'email' => $user->email,
            'role' => $user->role->value,
        ]);

        $this->assertSame($originalPassword, $user->fresh()->password);
    }
}
