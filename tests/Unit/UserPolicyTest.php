<?php

namespace Tests\Unit;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserPolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_can_view_any_user(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();

        $this->assertTrue($superAdmin->can('viewAny', User::class));
    }

    public function test_admin_can_view_any_user(): void
    {
        $admin = User::factory()->admin()->create();

        $this->assertTrue($admin->can('viewAny', User::class));
    }

    public function test_editor_cannot_view_any_user(): void
    {
        $editor = User::factory()->editor()->create();

        $this->assertFalse($editor->can('viewAny', User::class));
    }

    public function test_pending_cannot_view_any_user(): void
    {
        $pending = User::factory()->pending()->create();

        $this->assertFalse($pending->can('viewAny', User::class));
    }

    public function test_super_admin_can_update_admin(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();
        $admin = User::factory()->admin()->create();

        $this->assertTrue($superAdmin->can('update', $admin));
    }

    public function test_admin_cannot_update_another_admin(): void
    {
        $admin = User::factory()->admin()->create();
        $otherAdmin = User::factory()->admin()->create();

        $this->assertFalse($admin->can('update', $otherAdmin));
    }

    public function test_admin_cannot_update_self(): void
    {
        $admin = User::factory()->admin()->create();

        $this->assertFalse($admin->can('update', $admin));
    }

    public function test_admin_can_update_editor(): void
    {
        $admin = User::factory()->admin()->create();
        $editor = User::factory()->editor()->create();

        $this->assertTrue($admin->can('update', $editor));
    }

    public function test_admin_can_update_pending(): void
    {
        $admin = User::factory()->admin()->create();
        $pending = User::factory()->pending()->create();

        $this->assertTrue($admin->can('update', $pending));
    }

    public function test_editor_cannot_update_other_users(): void
    {
        $editor = User::factory()->editor()->create();
        $otherEditor = User::factory()->editor()->create();

        $this->assertFalse($editor->can('update', $otherEditor));
    }

    public function test_super_admin_can_delete_admin(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();
        $admin = User::factory()->admin()->create();

        $this->assertTrue($superAdmin->can('delete', $admin));
    }

    public function test_admin_cannot_delete_another_admin(): void
    {
        $admin = User::factory()->admin()->create();
        $otherAdmin = User::factory()->admin()->create();

        $this->assertFalse($admin->can('delete', $otherAdmin));
    }

    public function test_admin_cannot_delete_self(): void
    {
        $admin = User::factory()->admin()->create();

        $this->assertFalse($admin->can('delete', $admin));
    }

    public function test_admin_can_delete_editor(): void
    {
        $admin = User::factory()->admin()->create();
        $editor = User::factory()->editor()->create();

        $this->assertTrue($admin->can('delete', $editor));
    }

    public function test_admin_can_delete_pending(): void
    {
        $admin = User::factory()->admin()->create();
        $pending = User::factory()->pending()->create();

        $this->assertTrue($admin->can('delete', $pending));
    }

    public function test_super_admin_can_change_role_of_admin_to_editor(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();
        $admin = User::factory()->admin()->create();

        $this->assertTrue($superAdmin->can('changeRole', [$admin, UserRole::Editor]));
    }

    public function test_super_admin_can_change_role_of_pending_to_admin(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();
        $pending = User::factory()->pending()->create();

        $this->assertTrue($superAdmin->can('changeRole', [$pending, UserRole::Admin]));
    }

    public function test_admin_can_change_role_of_editor_to_pending(): void
    {
        $admin = User::factory()->admin()->create();
        $editor = User::factory()->editor()->create();

        $this->assertTrue($admin->can('changeRole', [$editor, UserRole::Pending]));
    }

    public function test_admin_cannot_change_role_of_another_admin(): void
    {
        $admin = User::factory()->admin()->create();
        $otherAdmin = User::factory()->admin()->create();

        $this->assertFalse($admin->can('changeRole', [$otherAdmin, UserRole::Editor]));
    }

    public function test_admin_cannot_change_own_role(): void
    {
        $admin = User::factory()->admin()->create();

        $this->assertFalse($admin->can('changeRole', [$admin, UserRole::SuperAdmin]));
    }

    public function test_admin_cannot_change_editor_to_super_admin(): void
    {
        $admin = User::factory()->admin()->create();
        $editor = User::factory()->editor()->create();

        $this->assertFalse($admin->can('changeRole', [$editor, UserRole::SuperAdmin]));
    }

    public function test_editor_cannot_change_anyones_role(): void
    {
        $editor = User::factory()->editor()->create();
        $otherEditor = User::factory()->editor()->create();

        $this->assertFalse($editor->can('changeRole', [$otherEditor, UserRole::Pending]));
    }

    public function test_super_admin_cannot_change_own_role(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();

        $this->assertFalse($superAdmin->can('changeRole', [$superAdmin, UserRole::Admin]));
    }
}
