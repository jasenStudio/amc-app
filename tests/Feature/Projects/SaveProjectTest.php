<?php

namespace Tests\Feature\Projects;

use App\Actions\Projects\SaveProject;
use App\Enums\ActiveStatus;
use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SaveProjectTest extends TestCase
{
    use RefreshDatabase;

    private SaveProject $action;

    protected function setUp(): void
    {
        parent::setUp();
        $this->action = new SaveProject;
    }

    public function test_creates_project_with_basic_data(): void
    {
        $project = $this->action->handle(
            project: null,
            data: [
                'title' => 'Test Project',
                'slug' => 'test-project',
                'description' => 'Test description',
                'client' => 'Test Client',
                'date' => '2024-01-15',
                'status' => 'active',
            ],
            galleryImages: [],
        );

        $this->assertNotNull($project->id);
        $this->assertSame('Test Project', $project->title);
        $this->assertSame('test-project', $project->slug);
        $this->assertSame('Test Client', $project->client);
        $this->assertSame(ActiveStatus::Active, $project->status);
    }

    public function test_creates_project_with_optional_fields(): void
    {
        $project = $this->action->handle(
            project: null,
            data: [
                'title' => 'Test Project',
                'slug' => 'test-project',
                'description' => 'Test description',
                'excerpt' => 'Short excerpt',
                'client' => 'Test Client',
                'location' => 'Test City',
                'date' => '2024-01-15',
                'status' => 'active',
                'featured' => true,
                'order' => 5,
                'title_seo' => 'SEO Title',
            ],
            galleryImages: [],
        );

        $this->assertSame('Short excerpt', $project->excerpt);
        $this->assertSame('Test City', $project->location);
        $this->assertTrue($project->featured);
        $this->assertSame(5, $project->order);
        $this->assertSame('SEO Title', $project->title_seo);
    }

    public function test_updates_existing_project(): void
    {
        $project = Project::factory()->create(['title' => 'Old Title']);

        $result = $this->action->handle(
            project: $project,
            data: [
                'title' => 'New Title',
                'slug' => $project->slug,
                'description' => $project->description,
                'client' => $project->client,
                'date' => $project->date->format('Y-m-d'),
                'status' => 'active',
            ],
            galleryImages: [],
        );

        $this->assertSame('New Title', $result->fresh()->title);
    }

    public function test_creates_gallery_images(): void
    {
        $project = $this->action->handle(
            project: null,
            data: [
                'title' => 'Test Project',
                'slug' => 'test-project',
                'description' => 'Test description',
                'client' => 'Test Client',
                'date' => '2024-01-15',
                'status' => 'active',
            ],
            galleryImages: [
                ['path' => 'images/test1.webp', 'order' => 0, 'is_cover' => true],
                ['path' => 'images/test2.webp', 'order' => 1, 'is_cover' => false],
            ],
        );

        $this->assertCount(2, $project->images);
        $this->assertTrue($project->images->where('image_path', 'images/test1.webp')->first()->is_cover);
        $this->assertFalse($project->images->where('image_path', 'images/test2.webp')->first()->is_cover);
    }

    public function test_only_one_cover_image_is_allowed(): void
    {
        $project = $this->action->handle(
            project: null,
            data: [
                'title' => 'Test Project',
                'slug' => 'test-project',
                'description' => 'Test description',
                'client' => 'Test Client',
                'date' => '2024-01-15',
                'status' => 'active',
            ],
            galleryImages: [
                ['path' => 'images/test1.webp', 'order' => 0, 'is_cover' => true],
                ['path' => 'images/test2.webp', 'order' => 1, 'is_cover' => true],
            ],
        );

        $coverCount = $project->images()->where('is_cover', true)->count();
        $this->assertSame(1, $coverCount);
    }

    public function test_sets_first_image_as_cover_when_none_specified(): void
    {
        $project = $this->action->handle(
            project: null,
            data: [
                'title' => 'Test Project',
                'slug' => 'test-project',
                'description' => 'Test description',
                'client' => 'Test Client',
                'date' => '2024-01-15',
                'status' => 'active',
            ],
            galleryImages: [
                ['path' => 'images/test1.webp', 'order' => 0, 'is_cover' => false],
                ['path' => 'images/test2.webp', 'order' => 1, 'is_cover' => false],
            ],
        );

        $firstImage = $project->images->where('image_path', 'images/test1.webp')->first();
        $this->assertTrue($firstImage->is_cover);
    }

    public function test_removes_deleted_gallery_images(): void
    {
        $project = Project::factory()->create();
        $project->images()->create([
            'image_path' => 'images/old.webp',
            'order' => 0,
            'is_cover' => true,
        ]);

        $this->action->handle(
            project: $project,
            data: [
                'title' => $project->title,
                'slug' => $project->slug,
                'description' => $project->description,
                'client' => $project->client,
                'date' => $project->date->format('Y-m-d'),
                'status' => 'active',
            ],
            galleryImages: [
                ['path' => 'images/new.webp', 'order' => 0, 'is_cover' => true],
            ],
        );

        $this->assertCount(1, $project->fresh()->images);
        $this->assertNull($project->fresh()->images->where('image_path', 'images/old.webp')->first());
        $this->assertNotNull($project->fresh()->images->where('image_path', 'images/new.webp')->first());
    }

    public function test_updates_existing_gallery_image(): void
    {
        $project = Project::factory()->create();
        $project->images()->create([
            'image_path' => 'images/existing.webp',
            'order' => 0,
            'is_cover' => false,
        ]);

        $this->action->handle(
            project: $project,
            data: [
                'title' => $project->title,
                'slug' => $project->slug,
                'description' => $project->description,
                'client' => $project->client,
                'date' => $project->date->format('Y-m-d'),
                'status' => 'active',
            ],
            galleryImages: [
                ['path' => 'images/existing.webp', 'order' => 0, 'is_cover' => true],
            ],
        );

        $image = $project->fresh()->images->where('image_path', 'images/existing.webp')->first();
        $this->assertTrue($image->is_cover);
    }
}
