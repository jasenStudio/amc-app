<?php

namespace Tests\Feature;

use App\Livewire\Ui\GalleryUploader;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class GalleryUploaderTest extends TestCase
{
    use RefreshDatabase;

    private string $disk = 'public';

    protected function setUp(): void
    {
        parent::setUp();
        App::setLocale('en');
        Storage::fake($this->disk);
    }

    public function test_oversized_image_reports_error_under_uploads_key(): void
    {
        $editor = User::factory()->editor()->create();
        $file = UploadedFile::fake()->image('big.png', 100, 100)->size(3000);

        Livewire::actingAs($editor)
            ->test(GalleryUploader::class, ['path' => 'services/webp'])
            ->set('uploads', [$file])
            ->assertHasErrors('uploads')
            ->assertSee('must not be larger than 2 MB.');
    }

    public function test_valid_images_are_stored_and_dispatch_event(): void
    {
        $editor = User::factory()->editor()->create();
        $file = UploadedFile::fake()->image('galeria.png', 1600, 900);

        Livewire::actingAs($editor)
            ->test(GalleryUploader::class, ['path' => 'services/webp', 'slugHint' => 'mi-servicio'])
            ->set('uploads', [$file])
            ->assertHasNoErrors()
            ->assertDispatched('gallery-image-uploaded');
    }

    public function test_gallery_accepts_smaller_landscape_image(): void
    {
        $editor = User::factory()->editor()->create();
        $file = UploadedFile::fake()->image('landscape.png', 800, 600);

        Livewire::actingAs($editor)
            ->test(GalleryUploader::class, ['path' => 'services/webp'])
            ->set('uploads', [$file])
            ->assertHasNoErrors()
            ->assertDispatched('gallery-image-uploaded');
    }

    public function test_gallery_accepts_portrait_image(): void
    {
        $editor = User::factory()->editor()->create();
        $file = UploadedFile::fake()->image('portrait.png', 600, 800);

        Livewire::actingAs($editor)
            ->test(GalleryUploader::class, ['path' => 'services/webp'])
            ->set('uploads', [$file])
            ->assertHasNoErrors()
            ->assertDispatched('gallery-image-uploaded');
    }

    public function test_gallery_rejects_image_below_minimum_dimensions(): void
    {
        $editor = User::factory()->editor()->create();
        $file = UploadedFile::fake()->image('tiny.png', 200, 150);

        Livewire::actingAs($editor)
            ->test(GalleryUploader::class, ['path' => 'services/webp'])
            ->set('uploads', [$file])
            ->assertHasErrors('uploads');
    }

    public function test_gallery_rejects_image_below_minimum_on_short_axis(): void
    {
        $editor = User::factory()->editor()->create();
        $file = UploadedFile::fake()->image('short.png', 400, 300);

        Livewire::actingAs($editor)
            ->test(GalleryUploader::class, ['path' => 'services/webp'])
            ->set('uploads', [$file])
            ->assertHasErrors('uploads');
    }
}
