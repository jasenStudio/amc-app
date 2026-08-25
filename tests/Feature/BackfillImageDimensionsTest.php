<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BackfillImageDimensionsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::disk('public')->makeDirectory('projects/webp/full');
        Storage::disk('public')->makeDirectory('services/webp/full');
    }

    public function test_backfills_project_image_dimensions(): void
    {
        $project = Project::factory()->create();
        $image = $project->images()->create([
            'image_path' => 'projects/webp/full/test.webp',
            'order' => 0,
            'is_cover' => true,
            'width' => null,
            'height' => null,
        ]);

        Storage::disk('public')->put(
            $image->image_path,
            $this->makeWebp(width: 1600, height: 1067)
        );

        $this->artisan('images:backfill-dimensions')
            ->assertSuccessful();

        $image->refresh();
        $this->assertSame(1600, $image->width);
        $this->assertSame(1067, $image->height);
    }

    public function test_backfills_service_image_dimensions(): void
    {
        $service = Service::factory()->create();
        $image = $service->images()->create([
            'image_path' => 'services/webp/full/test.webp',
            'order' => 0,
            'is_cover' => true,
            'width' => null,
            'height' => null,
        ]);

        Storage::disk('public')->put(
            $image->image_path,
            $this->makeWebp(width: 800, height: 600)
        );

        $this->artisan('images:backfill-dimensions')
            ->assertSuccessful();

        $image->refresh();
        $this->assertSame(800, $image->width);
        $this->assertSame(600, $image->height);
    }

    public function test_skips_images_with_missing_files(): void
    {
        $project = Project::factory()->create();
        $image = $project->images()->create([
            'image_path' => 'projects/webp/full/missing.webp',
            'order' => 0,
            'is_cover' => true,
            'width' => null,
            'height' => null,
        ]);

        $this->artisan('images:backfill-dimensions')
            ->assertSuccessful();

        $image->refresh();
        $this->assertNull($image->width);
        $this->assertNull($image->height);
    }

    public function test_skips_images_that_already_have_dimensions(): void
    {
        $project = Project::factory()->create();
        $image = $project->images()->create([
            'image_path' => 'projects/webp/full/test.webp',
            'order' => 0,
            'is_cover' => true,
            'width' => 100,
            'height' => 100,
        ]);

        Storage::disk('public')->put(
            $image->image_path,
            $this->makeWebp(width: 1600, height: 1067)
        );

        $this->artisan('images:backfill-dimensions')
            ->assertSuccessful();

        $image->refresh();
        $this->assertSame(100, $image->width);
        $this->assertSame(100, $image->height);
    }

    private function makeWebp(int $width, int $height): string
    {
        $im = imagecreatetruecolor($width, $height);
        imagefill($im, 0, 0, imagecolorallocate($im, random_int(0, 255), random_int(0, 255), random_int(0, 255)));

        ob_start();
        imagewebp($im);
        $contents = (string) ob_get_clean();
        imagedestroy($im);

        return $contents;
    }
}
