<?php

namespace Tests\Feature\Projects;

use App\Livewire\Projects\ProjectForm;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ProjectFormTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get(route('dashboard.projects.create'))
            ->assertRedirect(route('login'));
    }

    public function test_editor_cannot_access_create_form(): void
    {
        $editor = User::factory()->editor()->create();

        $this->actingAs($editor)
            ->get(route('dashboard.projects.create'))
            ->assertForbidden();
    }

    public function test_admin_can_access_create_form(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('dashboard.projects.create'))
            ->assertOk();
    }

    public function test_admin_can_access_edit_form(): void
    {
        $admin = User::factory()->admin()->create();
        $project = Project::factory()->create();

        $this->actingAs($admin)
            ->get(route('dashboard.projects.edit', $project))
            ->assertOk();
    }

    public function test_editor_cannot_access_edit_form(): void
    {
        $editor = User::factory()->editor()->create();
        $project = Project::factory()->create();

        $this->actingAs($editor)
            ->get(route('dashboard.projects.edit', $project))
            ->assertForbidden();
    }

    public function test_validation_requires_title(): void
    {
        $admin = User::factory()->admin()->create();

        Livewire::actingAs($admin)
            ->test(ProjectForm::class)
            ->set('title', '')
            ->call('save')
            ->assertHasErrors(['title' => 'required']);
    }

    public function test_validation_requires_unique_slug(): void
    {
        $admin = User::factory()->admin()->create();
        $existingProject = Project::factory()->create(['title' => 'Existing Project']);

        Livewire::actingAs($admin)
            ->test(ProjectForm::class)
            ->set('title', 'New Project')
            ->set('description', 'Test description')
            ->set('client', 'Test Client')
            ->set('date', '2024-01-15')
            ->set('slug', $existingProject->slug)
            ->call('save')
            ->assertHasErrors(['slug' => 'unique']);
    }

    public function test_validation_requires_description(): void
    {
        $admin = User::factory()->admin()->create();

        Livewire::actingAs($admin)
            ->test(ProjectForm::class)
            ->set('description', '')
            ->call('save')
            ->assertHasErrors(['description' => 'required']);
    }

    public function test_validation_requires_client(): void
    {
        $admin = User::factory()->admin()->create();

        Livewire::actingAs($admin)
            ->test(ProjectForm::class)
            ->set('client', '')
            ->call('save')
            ->assertHasErrors(['client' => 'required']);
    }

    public function test_validation_requires_date(): void
    {
        $admin = User::factory()->admin()->create();

        Livewire::actingAs($admin)
            ->test(ProjectForm::class)
            ->set('date', '')
            ->call('save')
            ->assertHasErrors(['date' => 'required']);
    }

    public function test_creates_project_successfully(): void
    {
        $admin = User::factory()->admin()->create();

        Livewire::actingAs($admin)
            ->test(ProjectForm::class)
            ->set('title', 'New Project')
            ->set('slug', 'new-project')
            ->set('description', 'Test description')
            ->set('client', 'Test Client')
            ->set('date', '2024-01-15')
            ->set('status', 'active')
            ->call('save')
            ->assertRedirect(route('dashboard.projects.index'));

        $this->assertDatabaseHas('projects', [
            'title' => 'New Project',
            'slug' => 'new-project',
            'client' => 'Test Client',
        ]);
    }

    public function test_updates_project_successfully(): void
    {
        $admin = User::factory()->admin()->create();
        $project = Project::factory()->create(['title' => 'Old Title']);

        Livewire::actingAs($admin)
            ->test(ProjectForm::class, ['projectId' => $project->id])
            ->set('title', 'New Title')
            ->call('save')
            ->assertRedirect(route('dashboard.projects.index'));

        $this->assertDatabaseHas('projects', [
            'id' => $project->id,
            'title' => 'New Title',
        ]);
    }

    public function test_optional_fields_can_be_empty(): void
    {
        $admin = User::factory()->admin()->create();

        Livewire::actingAs($admin)
            ->test(ProjectForm::class)
            ->set('title', 'New Project')
            ->set('slug', 'new-project')
            ->set('description', 'Test description')
            ->set('client', 'Test Client')
            ->set('date', '2024-01-15')
            ->set('excerpt', '')
            ->set('location', '')
            ->set('title_seo', '')
            ->set('status', 'active')
            ->call('save')
            ->assertRedirect(route('dashboard.projects.index'));

        $this->assertDatabaseHas('projects', [
            'title' => 'New Project',
            'excerpt' => null,
            'location' => null,
            'title_seo' => null,
        ]);
    }

    public function test_slug_is_auto_generated_from_title_for_new_projects(): void
    {
        $admin = User::factory()->admin()->create();

        Livewire::actingAs($admin)
            ->test(ProjectForm::class)
            ->set('title', 'My New Project')
            ->assertSet('slug', 'my-new-project');
    }

    public function test_regenerate_slug_works(): void
    {
        $admin = User::factory()->admin()->create();
        $project = Project::factory()->create(['title' => 'Original Title', 'slug' => 'original-title']);

        Livewire::actingAs($admin)
            ->test(ProjectForm::class, ['projectId' => $project->id])
            ->set('title', 'Updated Title')
            ->call('regenerateSlug')
            ->assertSet('slug', 'updated-title');
    }

    public function test_required_badge_is_rendered_for_required_fields(): void
    {
        $admin = User::factory()->admin()->create();

        $html = Livewire::actingAs($admin)
            ->test(ProjectForm::class)
            ->html();

        // Cada campo requerido renderiza el badge en la pill del label y como
        // atributo `label:badge` reenviado al control (2 apariciones por campo).
        $this->assertSame(12, substr_count($html, __('required_field')));
    }

    public function test_slug_description_hint_is_rendered(): void
    {
        $admin = User::factory()->admin()->create();

        Livewire::actingAs($admin)
            ->test(ProjectForm::class)
            ->assertSee(__('slug_hint'));
    }

    public function test_video_url_description_hint_is_rendered(): void
    {
        $admin = User::factory()->admin()->create();

        Livewire::actingAs($admin)
            ->test(ProjectForm::class)
            ->assertSee(__('video_url_hint'));
    }

    public function test_error_summary_renders_after_invalid_submit(): void
    {
        $admin = User::factory()->admin()->create();

        Livewire::actingAs($admin)
            ->test(ProjectForm::class)
            ->set('title', '')
            ->call('save')
            ->assertHasErrors()
            ->assertSee(__('review_form_errors'));
    }

    public function test_real_time_validation_on_title_update(): void
    {
        $admin = User::factory()->admin()->create();

        Livewire::actingAs($admin)
            ->test(ProjectForm::class)
            ->set('title', '')
            ->assertHasErrors(['title' => 'required']);
    }

    public function test_real_time_validation_on_video_url_pattern(): void
    {
        $admin = User::factory()->admin()->create();

        Livewire::actingAs($admin)
            ->test(ProjectForm::class)
            ->set('title', 'A project')
            ->set('video_url', 'not-a-url')
            ->assertHasErrors(['video_url' => 'url']);
    }

    public function test_real_time_validation_on_date_pattern(): void
    {
        $admin = User::factory()->admin()->create();

        Livewire::actingAs($admin)
            ->test(ProjectForm::class)
            ->set('title', 'A project')
            ->set('date', 'not-a-date')
            ->assertHasErrors(['date' => 'date']);
    }
}
