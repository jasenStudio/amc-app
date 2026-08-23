<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

class RoleGateTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_passes_admin_and_manage_posts_gates(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();

        $this->actingAs($superAdmin);

        $this->assertTrue($superAdmin->can('admin'));
        $this->assertTrue($superAdmin->can('manage-posts'));
    }

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

    public function test_pending_user_fails_all_gates(): void
    {
        $pending = User::factory()->pending()->create();

        $this->actingAs($pending);

        $this->assertFalse($pending->can('admin'));
        $this->assertFalse($pending->can('manage-posts'));
    }

    public function test_guest_fails_all_gates(): void
    {
        $this->assertFalse(Gate::allows('admin'));
        $this->assertFalse(Gate::allows('manage-posts'));
    }

    public function test_pending_user_is_redirected_to_pending_approval_from_dashboard(): void
    {
        $pending = User::factory()->pending()->create();

        $this->actingAs($pending)
            ->get(route('dashboard'))
            ->assertRedirect(route('pending.approval'));
    }

    public function test_pending_user_can_logout_from_pending_approval_page(): void
    {
        $pending = User::factory()->pending()->create();

        $this->actingAs($pending)
            ->get(route('pending.approval'))
            ->assertOk()
            ->assertSee(__('Log out'), false);

        $this->actingAs($pending)
            ->post(route('logout'))
            ->assertRedirect('/');

        $this->assertGuest();
    }

    public function test_dashboard_is_accessible_for_super_admin(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();

        $this->actingAs($superAdmin)
            ->get(route('dashboard'))
            ->assertOk();
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
