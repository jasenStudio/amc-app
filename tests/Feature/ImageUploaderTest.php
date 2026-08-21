<?php

namespace Tests\Feature;

use App\Livewire\Ui\ImageUploader;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class ImageUploaderTest extends TestCase
{
    use RefreshDatabase;

    private string $disk = 'public';

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake($this->disk);
    }

    public function test_remove_image_deletes_pending_uploads(): void
    {
        $editor = User::factory()->editor()->create();
        Storage::disk($this->disk)->put('blog/webp/thumbs/pending.webp', 'thumb');
        Storage::disk($this->disk)->put('blog/webp/full/pending.webp', 'full');

        Livewire::actingAs($editor)
            ->test(ImageUploader::class, ['path' => 'blog/webp'])
            ->set('pendingThumbPath', 'blog/webp/thumbs/pending.webp')
            ->set('pendingFullPath', 'blog/webp/full/pending.webp')
            ->call('removeImage');

        Storage::disk($this->disk)->assertMissing('blog/webp/thumbs/pending.webp');
        Storage::disk($this->disk)->assertMissing('blog/webp/full/pending.webp');
    }

    public function test_remove_image_keeps_persisted_cover_files(): void
    {
        $editor = User::factory()->editor()->create();
        Storage::disk($this->disk)->put('blog/webp/thumbs/saved.webp', 'thumb');
        Storage::disk($this->disk)->put('blog/webp/full/saved.webp', 'full');

        Livewire::actingAs($editor)
            ->test(ImageUploader::class, [
                'path' => 'blog/webp',
                'existingThumbUrl' => '/storage/blog/webp/thumbs/saved.webp',
                'existingFullUrl' => '/storage/blog/webp/full/saved.webp',
            ])
            ->call('removeImage');

        Storage::disk($this->disk)->assertExists('blog/webp/thumbs/saved.webp');
        Storage::disk($this->disk)->assertExists('blog/webp/full/saved.webp');
    }

    public function test_uploading_an_image_stores_webp_files_and_dispatches_event(): void
    {
        $editor = User::factory()->editor()->create();
        $file = new UploadedFile($this->makePng(), 'cover.png', 'image/png', null, true);

        Livewire::actingAs($editor)
            ->test(ImageUploader::class, ['path' => 'blog/webp', 'slugHint' => 'mi-post'])
            ->call('uploadImage', $file)
            ->assertHasNoErrors()
            ->assertDispatched('image-uploaded');

        $files = Storage::disk($this->disk)->allFiles('blog/webp');
        $this->assertNotEmpty($files);
        foreach ($files as $path) {
            $this->assertStringEndsWith('.webp', $path);
        }
    }

    private function makePng(int $width = 1600, int $height = 900): string
    {
        $path = tempnam(sys_get_temp_dir(), 'png_').'.png';
        $im = imagecreatetruecolor($width, $height);
        imagefill($im, 0, 0, imagecolorallocate($im, 100, 100, 100));
        imagepng($im, $path);
        imagedestroy($im);

        return $path;
    }
}
