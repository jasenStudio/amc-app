<?php

namespace Tests\Feature\Dashboard;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RouteAuthorizationMatrixTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return array<string, string>
     */
    private function dashboardRoutes(): array
    {
        return [
            'dashboard' => route('dashboard'),
        ];
    }

    /**
     * @return array<string, string>
     */
    private function blogRoutes(): array
    {
        return [
            'blog.index' => route('blog.index'),
            'blog.create' => route('blog.create'),
        ];
    }

    /**
     * @return array<string, string>
     */
    private function adminRoutes(): array
    {
        return [
            'users.index' => route('dashboard.users.index'),
            'users.create' => route('dashboard.users.create'),
            'projects.index' => route('dashboard.projects.index'),
            'services.index' => route('dashboard.services.index'),
            'tags.index' => route('dashboard.tags.index'),
            'tags.create' => route('dashboard.tags.create'),
        ];
    }

    public function test_super_admin_accesses_all_protected_routes(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();

        foreach ($this->dashboardRoutes() as $name => $route) {
            $this->actingAs($superAdmin)->get($route)->assertOk();
        }

        foreach ($this->blogRoutes() as $name => $route) {
            $this->actingAs($superAdmin)->get($route)->assertOk();
        }

        foreach ($this->adminRoutes() as $name => $route) {
            $this->actingAs($superAdmin)->get($route)->assertOk();
        }
    }

    public function test_admin_accesses_all_protected_routes(): void
    {
        $admin = User::factory()->admin()->create();

        foreach ($this->dashboardRoutes() as $name => $route) {
            $this->actingAs($admin)->get($route)->assertOk();
        }

        foreach ($this->blogRoutes() as $name => $route) {
            $this->actingAs($admin)->get($route)->assertOk();
        }

        foreach ($this->adminRoutes() as $name => $route) {
            $this->actingAs($admin)->get($route)->assertOk();
        }
    }

    public function test_editor_accesses_dashboard_and_blog_but_not_admin_routes(): void
    {
        $editor = User::factory()->editor()->create();

        foreach ($this->dashboardRoutes() as $name => $route) {
            $this->actingAs($editor)->get($route)->assertOk();
        }

        foreach ($this->blogRoutes() as $name => $route) {
            $this->actingAs($editor)->get($route)->assertOk();
        }

        foreach ($this->adminRoutes() as $name => $route) {
            $this->actingAs($editor)->get($route)->assertForbidden();
        }
    }

    public function test_pending_user_is_redirected_to_pending_approval_on_all_protected_routes(): void
    {
        $pending = User::factory()->pending()->create();

        foreach ($this->dashboardRoutes() as $name => $route) {
            $this->actingAs($pending)->get($route)->assertRedirect(route('pending.approval'));
        }

        foreach ($this->blogRoutes() as $name => $route) {
            $this->actingAs($pending)->get($route)->assertRedirect(route('pending.approval'));
        }

        foreach ($this->adminRoutes() as $name => $route) {
            $this->actingAs($pending)->get($route)->assertRedirect(route('pending.approval'));
        }
    }

    public function test_pending_approval_page_has_no_redirect_loop_for_pending_user(): void
    {
        $pending = User::factory()->pending()->create();

        $this->actingAs($pending)
            ->get(route('pending.approval'), ['X-Requested-With' => ''])
            ->assertOk();
    }

    public function test_guests_are_redirected_to_login_on_all_protected_routes(): void
    {
        foreach ($this->dashboardRoutes() as $route) {
            $this->get($route)->assertRedirect(route('login'));
        }

        foreach ($this->blogRoutes() as $route) {
            $this->get($route)->assertRedirect(route('login'));
        }

        foreach ($this->adminRoutes() as $route) {
            $this->get($route)->assertRedirect(route('login'));
        }
    }

    public function test_editor_cannot_access_user_form_routes(): void
    {
        $editor = User::factory()->editor()->create();
        $target = User::factory()->editor()->create();

        $this->actingAs($editor)
            ->get(route('dashboard.users.create'))
            ->assertForbidden();

        $this->actingAs($editor)
            ->get(route('dashboard.users.edit', ['user' => $target->id]))
            ->assertForbidden();
    }

    public function test_admin_can_access_user_form_routes_for_manageable_user(): void
    {
        $admin = User::factory()->admin()->create();
        $editor = User::factory()->editor()->create();

        $this->actingAs($admin)
            ->get(route('dashboard.users.create'))
            ->assertOk();

        $this->actingAs($admin)
            ->get(route('dashboard.users.edit', ['user' => $editor->id]))
            ->assertOk();
    }

    public function test_dashboard_images_store_redirects_pending_user(): void
    {
        $pending = User::factory()->pending()->create();

        $this->actingAs($pending)
            ->postJson(route('dashboard.images.store'))
            ->assertRedirect(route('pending.approval'));
    }

    public function test_dashboard_images_store_is_accessible_by_editor(): void
    {
        $editor = User::factory()->editor()->create();

        $this->actingAs($editor)
            ->postJson(route('dashboard.images.store'))
            ->assertStatus(422);
    }

    public function test_dashboard_images_store_is_accessible_by_admin(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->postJson(route('dashboard.images.store'))
            ->assertStatus(422);
    }

    public function test_dashboard_images_store_is_accessible_by_super_admin(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();

        $this->actingAs($superAdmin)
            ->postJson(route('dashboard.images.store'))
            ->assertStatus(422);
    }
}
