<?php

namespace Tests\Feature\Projects;

use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicProjectsTest extends TestCase
{
    use RefreshDatabase;

    public function test_projects_index_page_is_accessible(): void
    {
        $this->get(route('projects'))
            ->assertOk();
    }

    public function test_projects_index_shows_active_projects(): void
    {
        $activeProject = Project::factory()->active()->create(['title' => 'Active Project']);
        $inactiveProject = Project::factory()->inactive()->create(['title' => 'Inactive Project']);

        $this->get(route('projects'))
            ->assertOk()
            ->assertSee('Active Project')
            ->assertDontSee('Inactive Project');
    }

    public function test_projects_index_shows_featured_badge(): void
    {
        $featuredProject = Project::factory()->featured()->create(['title' => 'Featured Project']);

        $this->get(route('projects'))
            ->assertOk()
            ->assertSee('Featured Project')
            ->assertSee('Featured');
    }

    public function test_project_show_page_is_accessible(): void
    {
        $project = Project::factory()->active()->create(['slug' => 'test-project']);

        $this->get(route('projects.show', $project->slug))
            ->assertOk()
            ->assertSee($project->title);
    }

    public function test_project_show_returns_404_for_inactive_project(): void
    {
        $project = Project::factory()->inactive()->create(['slug' => 'inactive-project']);

        $this->get(route('projects.show', $project->slug))
            ->assertNotFound();
    }

    public function test_project_show_returns_404_for_nonexistent_slug(): void
    {
        $this->get(route('projects.show', 'nonexistent-slug'))
            ->assertNotFound();
    }

    public function test_project_show_displays_seo_title(): void
    {
        $project = Project::factory()->active()->create([
            'title' => 'Public Title',
            'title_seo' => 'SEO Title',
        ]);

        $this->get(route('projects.show', $project->slug))
            ->assertOk()
            ->assertSee('SEO Title');
    }

    public function test_project_show_falls_back_to_title_when_seo_title_empty(): void
    {
        $project = Project::factory()->active()->create([
            'title' => 'Public Title',
            'title_seo' => null,
        ]);

        $this->get(route('projects.show', $project->slug))
            ->assertOk()
            ->assertSee('Public Title');
    }

    public function test_project_show_displays_gallery_images(): void
    {
        $project = Project::factory()->active()->create();
        $project->images()->createMany([
            ['image_path' => 'images/cover.webp', 'order' => 0, 'is_cover' => true],
            ['image_path' => 'images/gallery1.webp', 'order' => 1, 'is_cover' => false],
            ['image_path' => 'images/gallery2.webp', 'order' => 2, 'is_cover' => false],
        ]);

        $this->get(route('projects.show', $project->slug))
            ->assertOk()
            ->assertSee(__('project_documentation'));
    }

    public function test_project_show_displays_client_and_location(): void
    {
        $project = Project::factory()->active()->create([
            'client' => 'Test Client',
            'location' => 'Test City',
        ]);

        $this->get(route('projects.show', $project->slug))
            ->assertOk()
            ->assertSee('Test Client')
            ->assertSee('Test City');
    }

    public function test_project_show_displays_project_date(): void
    {
        $project = Project::factory()->active()->create([
            'date' => '2024-06-15',
        ]);

        $this->get(route('projects.show', $project->slug))
            ->assertOk()
            ->assertSee('Jun 15, 2024');
    }

    public function test_project_show_displays_cover_image(): void
    {
        $project = Project::factory()->active()->create();
        $project->images()->create([
            'image_path' => 'images/cover.webp',
            'order' => 0,
            'is_cover' => true,
        ]);

        $response = $this->get(route('projects.show', $project->slug));

        $response->assertOk();
    }

    public function test_project_show_includes_og_image_meta(): void
    {
        $project = Project::factory()->active()->create();
        $project->images()->create([
            'image_path' => 'images/cover.webp',
            'order' => 0,
            'is_cover' => true,
        ]);

        $this->get(route('projects.show', $project->slug))
            ->assertOk()
            ->assertSee('og:image', false);
    }
}
