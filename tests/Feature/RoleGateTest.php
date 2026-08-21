<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

class RoleGateTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_user_passes_admin_and_manage_posts_gates(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin);

        $this->assertTrue($admin->can('admin'));
        $this->assertTrue($admin->can('manage-posts'));
    }

    public function test_editor_user_fails_admin_gate_but_passes_manage_posts(): void
    {
        $editor = User::factory()->editor()->create();

        $this->actingAs($editor);

        $this->assertFalse($editor->can('admin'));
        $this->assertTrue($editor->can('manage-posts'));
    }

    public function test_freshly_registered_user_without_role_fails_all_gates(): void
    {
        $user = User::factory()->create();

        $this->assertNull($user->role);

        $this->actingAs($user);

        $this->assertFalse($user->can('admin'));
        $this->assertFalse($user->can('manage-posts'));
    }

    public function test_guest_fails_all_gates(): void
    {
        $this->assertFalse(Gate::allows('admin'));
        $this->assertFalse(Gate::allows('manage-posts'));
    }

    public function test_dashboard_returns_403_for_user_without_role(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertForbidden();
    }

    public function test_dashboard_is_accessible_for_admin(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('dashboard'))
            ->assertOk();
    }

    public function test_dashboard_is_accessible_for_editor(): void
    {
        $editor = User::factory()->editor()->create();

        $this->actingAs($editor)
            ->get(route('dashboard'))
            ->assertOk();
    }
}
