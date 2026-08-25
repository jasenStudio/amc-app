<?php

namespace Tests\Feature\Services;

use App\Actions\Services\SaveService;
use App\Enums\ActiveStatus;
use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SaveServiceTest extends TestCase
{
    use RefreshDatabase;

    private SaveService $action;

    protected function setUp(): void
    {
        parent::setUp();
        $this->action = new SaveService;
    }

    public function test_creates_service_with_basic_data(): void
    {
        $service = $this->action->handle(
            service: null,
            data: [
                'title' => 'Test Service',
                'slug' => 'test-service',
                'description' => 'Test description',
                'status' => 'active',
            ],
            galleryImages: [],
        );

        $this->assertNotNull($service->id);
        $this->assertSame('Test Service', $service->title);
        $this->assertSame('test-service', $service->slug);
        $this->assertSame(ActiveStatus::Active, $service->status);
    }

    public function test_creates_service_with_optional_fields(): void
    {
        $service = $this->action->handle(
            service: null,
            data: [
                'title' => 'Test Service',
                'slug' => 'test-service',
                'description' => 'Test description',
                'excerpt' => 'Short excerpt',
                'price' => 1500.50,
                'status' => 'active',
                'featured' => true,
                'order' => 5,
                'title_seo' => 'SEO Title',
            ],
            galleryImages: [],
        );

        $this->assertSame('Short excerpt', $service->excerpt);
        $this->assertSame('1500.50', $service->price);
        $this->assertTrue($service->featured);
        $this->assertSame(5, $service->order);
        $this->assertSame('SEO Title', $service->title_seo);
    }

    public function test_updates_existing_service(): void
    {
        $service = Service::factory()->create(['title' => 'Old Title']);

        $result = $this->action->handle(
            service: $service,
            data: [
                'title' => 'New Title',
                'slug' => $service->slug,
                'description' => $service->description,
                'status' => 'active',
            ],
            galleryImages: [],
        );

        $this->assertSame('New Title', $result->fresh()->title);
    }

    public function test_creates_gallery_images(): void
    {
        $service = $this->action->handle(
            service: null,
            data: [
                'title' => 'Test Service',
                'slug' => 'test-service',
                'description' => 'Test description',
                'status' => 'active',
            ],
            galleryImages: [
                ['path' => 'images/test1.webp', 'order' => 0, 'is_cover' => true],
                ['path' => 'images/test2.webp', 'order' => 1, 'is_cover' => false],
            ],
        );

        $this->assertCount(2, $service->images);
        $this->assertTrue($service->images->where('image_path', 'images/test1.webp')->first()->is_cover);
        $this->assertFalse($service->images->where('image_path', 'images/test2.webp')->first()->is_cover);
    }

    public function test_only_one_cover_image_is_allowed(): void
    {
        $service = $this->action->handle(
            service: null,
            data: [
                'title' => 'Test Service',
                'slug' => 'test-service',
                'description' => 'Test description',
                'status' => 'active',
            ],
            galleryImages: [
                ['path' => 'images/test1.webp', 'order' => 0, 'is_cover' => true],
                ['path' => 'images/test2.webp', 'order' => 1, 'is_cover' => true],
            ],
        );

        $coverCount = $service->images()->where('is_cover', true)->count();
        $this->assertSame(1, $coverCount);
    }

    public function test_sets_first_image_as_cover_when_none_specified(): void
    {
        $service = $this->action->handle(
            service: null,
            data: [
                'title' => 'Test Service',
                'slug' => 'test-service',
                'description' => 'Test description',
                'status' => 'active',
            ],
            galleryImages: [
                ['path' => 'images/test1.webp', 'order' => 0, 'is_cover' => false],
                ['path' => 'images/test2.webp', 'order' => 1, 'is_cover' => false],
            ],
        );

        $firstImage = $service->images->where('image_path', 'images/test1.webp')->first();
        $this->assertTrue($firstImage->is_cover);
    }

    public function test_removes_deleted_gallery_images(): void
    {
        $service = Service::factory()->create();
        $service->images()->create([
            'image_path' => 'images/old.webp',
            'order' => 0,
            'is_cover' => true,
        ]);

        $this->action->handle(
            service: $service,
            data: [
                'title' => $service->title,
                'slug' => $service->slug,
                'description' => $service->description,
                'status' => 'active',
            ],
            galleryImages: [
                ['path' => 'images/new.webp', 'order' => 0, 'is_cover' => true],
            ],
        );

        $this->assertCount(1, $service->fresh()->images);
        $this->assertNull($service->fresh()->images->where('image_path', 'images/old.webp')->first());
        $this->assertNotNull($service->fresh()->images->where('image_path', 'images/new.webp')->first());
    }

    public function test_stores_gallery_image_dimensions(): void
    {
        $service = $this->action->handle(
            service: null,
            data: [
                'title' => 'Test Service',
                'slug' => 'test-service',
                'description' => 'Test description',
                'status' => 'active',
            ],
            galleryImages: [
                ['path' => 'images/test1.webp', 'order' => 0, 'is_cover' => true, 'width' => 1600, 'height' => 1067],
                ['path' => 'images/test2.webp', 'order' => 1, 'is_cover' => false, 'width' => 800, 'height' => 600],
            ],
        );

        $firstImage = $service->images->where('image_path', 'images/test1.webp')->first();
        $secondImage = $service->images->where('image_path', 'images/test2.webp')->first();

        $this->assertSame(1600, $firstImage->width);
        $this->assertSame(1067, $firstImage->height);
        $this->assertSame(800, $secondImage->width);
        $this->assertSame(600, $secondImage->height);
    }

    public function test_updates_gallery_image_dimensions(): void
    {
        $service = Service::factory()->create();
        $service->images()->create([
            'image_path' => 'images/existing.webp',
            'order' => 0,
            'is_cover' => true,
            'width' => 100,
            'height' => 100,
        ]);

        $this->action->handle(
            service: $service,
            data: [
                'title' => $service->title,
                'slug' => $service->slug,
                'description' => $service->description,
                'status' => 'active',
            ],
            galleryImages: [
                ['path' => 'images/existing.webp', 'order' => 0, 'is_cover' => true, 'width' => 1600, 'height' => 1067],
            ],
        );

        $image = $service->fresh()->images->where('image_path', 'images/existing.webp')->first();
        $this->assertSame(1600, $image->width);
        $this->assertSame(1067, $image->height);
    }
}
