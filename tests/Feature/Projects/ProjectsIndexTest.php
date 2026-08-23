<?php

namespace Tests\Feature\Projects;

use App\Livewire\Projects\ProjectsIndex;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ProjectsIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get(route('dashboard.projects.index'))
            ->assertRedirect(route('login'));
    }

    public function test_pending_user_is_redirected_to_pending_approval(): void
    {
        $pending = User::factory()->pending()->create();

        $this->actingAs($pending)
            ->get(route('dashboard.projects.index'))
            ->assertRedirect(route('pending.approval'));
    }

    public function test_editor_cannot_view_index(): void
    {
        $editor = User::factory()->editor()->create();

        $this->actingAs($editor)
            ->get(route('dashboard.projects.index'))
            ->assertForbidden();
    }

    public function test_admin_can_view_index(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('dashboard.projects.index'))
            ->assertOk();
    }

    public function test_super_admin_can_view_index(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();

        $this->actingAs($superAdmin)
            ->get(route('dashboard.projects.index'))
            ->assertOk();
    }

    public function test_search_filters_by_title(): void
    {
        $admin = User::factory()->admin()->create();
        Project::factory()->create(['title' => 'Laravel Project']);
        Project::factory()->create(['title' => 'Other Project']);

        Livewire::actingAs($admin)
            ->test(ProjectsIndex::class)
            ->set('search', 'Laravel')
            ->assertViewHas('projects', fn ($projects) => $projects->total() === 1);
    }

    public function test_search_filters_by_client(): void
    {
        $admin = User::factory()->admin()->create();
        Project::factory()->create(['client' => 'Acme Corp']);
        Project::factory()->create(['client' => 'Other Client']);

        Livewire::actingAs($admin)
            ->test(ProjectsIndex::class)
            ->set('search', 'Acme')
            ->assertViewHas('projects', fn ($projects) => $projects->total() === 1);
    }

    public function test_status_filter(): void
    {
        $admin = User::factory()->admin()->create();
        Project::factory()->count(2)->active()->create();
        Project::factory()->count(3)->inactive()->create();

        Livewire::actingAs($admin)
            ->test(ProjectsIndex::class)
            ->set('status', 'active')
            ->assertViewHas('projects', fn ($projects) => $projects->total() === 2)
            ->set('status', 'inactive')
            ->assertViewHas('projects', fn ($projects) => $projects->total() === 3);
    }

    public function test_featured_filter(): void
    {
        $admin = User::factory()->admin()->create();
        Project::factory()->count(2)->featured()->create();
        Project::factory()->count(3)->create();

        Livewire::actingAs($admin)
            ->test(ProjectsIndex::class)
            ->set('featured', 'yes')
            ->assertViewHas('projects', fn ($projects) => $projects->total() === 2);
    }

    public function test_location_filter(): void
    {
        $admin = User::factory()->admin()->create();
        Project::factory()->create(['location' => 'Madrid']);
        Project::factory()->create(['location' => 'Barcelona']);
        Project::factory()->create(['location' => null]);

        Livewire::actingAs($admin)
            ->test(ProjectsIndex::class)
            ->set('location', 'Madrid')
            ->assertViewHas('projects', fn ($projects) => $projects->total() === 1);
    }

    public function test_admin_can_soft_delete_project(): void
    {
        $admin = User::factory()->admin()->create();
        $project = Project::factory()->create();

        Livewire::actingAs($admin)
            ->test(ProjectsIndex::class)
            ->call('delete', $project->id);

        $this->assertSoftDeleted('projects', ['id' => $project->id]);
    }

    public function test_editor_cannot_delete_project(): void
    {
        $editor = User::factory()->editor()->create();
        $project = Project::factory()->create();

        Livewire::actingAs($editor)
            ->test(ProjectsIndex::class)
            ->call('delete', $project->id);

        $this->assertNotSoftDeleted('projects', ['id' => $project->id]);
    }
}
