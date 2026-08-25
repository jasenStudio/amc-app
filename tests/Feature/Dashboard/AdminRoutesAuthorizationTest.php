<?php

namespace Tests\Feature\Dashboard;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminRoutesAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return array<string, string>
     */
    private function adminRoutes(): array
    {
        return [
            'users' => route('dashboard.users.index'),
            'projects' => route('dashboard.projects.index'),
            'services' => route('dashboard.services.index'),
            'tags' => route('dashboard.tags.index'),
        ];
    }

    public function test_editor_is_forbidden_on_admin_dashboard_routes(): void
    {
        $editor = User::factory()->editor()->create();

        foreach ($this->adminRoutes() as $route) {
            $this->actingAs($editor)
                ->get($route)
                ->assertForbidden();
        }
    }

    public function test_pending_user_is_redirected_to_pending_approval_on_admin_dashboard_routes(): void
    {
        $pending = User::factory()->pending()->create();

        foreach ($this->adminRoutes() as $route) {
            $this->actingAs($pending)
                ->get($route)
                ->assertRedirect(route('pending.approval'));
        }
    }

    public function test_guests_are_redirected_to_login_on_admin_dashboard_routes(): void
    {
        foreach ($this->adminRoutes() as $route) {
            $this->get($route)
                ->assertRedirect(route('login'));
        }
    }

    public function test_super_admin_can_access_all_admin_dashboard_routes(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();

        foreach ($this->adminRoutes() as $route) {
            $this->actingAs($superAdmin)
                ->get($route)
                ->assertOk();
        }
    }

    public function test_admin_can_access_all_admin_dashboard_routes(): void
    {
        $admin = User::factory()->admin()->create();

        foreach ($this->adminRoutes() as $route) {
            $this->actingAs($admin)
                ->get($route)
                ->assertOk();
        }
    }

    public function test_editor_sidebar_does_not_render_admin_links(): void
    {
        $editor = User::factory()->editor()->create();

        $this->actingAs($editor)
            ->get(route('blog.index'))
            ->assertOk()
            ->assertSee(route('blog.index'), false)
            ->assertDontSee(route('dashboard.users.index'), false)
            ->assertDontSee(route('dashboard.projects.index'), false)
            ->assertDontSee(route('dashboard.services.index'), false)
            ->assertDontSee(route('dashboard.tags.index'), false);
    }

    public function test_admin_sidebar_renders_all_links(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee(route('blog.index'), false)
            ->assertSee(route('dashboard.users.index'), false)
            ->assertSee(route('dashboard.projects.index'), false)
            ->assertSee(route('dashboard.services.index'), false)
            ->assertSee(route('dashboard.tags.index'), false);
    }

    public function test_editor_can_still_upload_dashboard_images(): void
    {
        $editor = User::factory()->editor()->create();

        $this->actingAs($editor)
            ->postJson(route('dashboard.images.store'))
            ->assertStatus(422)
            ->assertJsonValidationErrors(['upload', 'path']);
    }
}
