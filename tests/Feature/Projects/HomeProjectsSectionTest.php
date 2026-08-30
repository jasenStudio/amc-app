<?php

namespace Tests\Feature\Projects;

use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomeProjectsSectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_hides_projects_section_when_no_featured_projects(): void
    {
        Project::factory()->active()->create(['featured' => false]);

        $this->get(route('home'))
            ->assertOk()
            ->assertDontSee('id="projects"', false)
            ->assertDontSee(__('projects_view_all'), false);
    }

    public function test_home_shows_projects_section_when_featured_projects_exist(): void
    {
        Project::factory()->active()->featured()->create(['title' => 'Featured Project']);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('id="projects"', false)
            ->assertSee(__('projects_view_all'), false)
            ->assertSee('Featured Project');
    }
}
