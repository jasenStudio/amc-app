<?php

namespace Tests\Unit;

use App\Actions\Images\ConvertImageToWebp;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ConvertImageToWebpTest extends TestCase
{
    use RefreshDatabase;

    private string $disk = 'public';

    protected function setUp(): void
    {
        parent::setUp();
        Storage::disk($this->disk)->makeDirectory('blog/webp');
    }

    public function test_converts_upload_to_webp_thumb_and_full(): void
    {
        $source = $this->makePng(width: 1200, height: 800);
        $file = new UploadedFile($source, 'cover.png', 'image/png', null, true);

        $action = app(ConvertImageToWebp::class);
        $paths = $action($file, 'blog/webp', $this->disk);

        $this->assertStringStartsWith('blog/webp/thumbs/', $paths['thumb']);
        $this->assertStringEndsWith('.webp', $paths['thumb']);
        $this->assertStringStartsWith('blog/webp/full/', $paths['full']);
        $this->assertStringEndsWith('.webp', $paths['full']);
        $this->assertSame('image/webp', Storage::disk($this->disk)->mimeType($paths['thumb']));
        $this->assertSame('image/webp', Storage::disk($this->disk)->mimeType($paths['full']));
    }

    public function test_generates_thumb_at_640px_and_full_at_1600px(): void
    {
        $source = $this->makePng(width: 2400, height: 1600);
        $file = new UploadedFile($source, 'cover.png', 'image/png', null, true);

        $action = app(ConvertImageToWebp::class);
        $paths = $action($file, 'blog/webp', $this->disk);

        $tmpThumb = $this->copyToTemp($paths['thumb']);
        $tmpFull = $this->copyToTemp($paths['full']);

        $thumbSize = getimagesize($tmpThumb);
        $fullSize = getimagesize($tmpFull);

        $this->assertSame(640, $thumbSize[0]);
        $this->assertSame(1600, $fullSize[0]);
        $this->assertSame('image/webp', $thumbSize['mime']);
        $this->assertSame('image/webp', $fullSize['mime']);
    }

    public function test_basename_is_unique_across_calls(): void
    {
        $file = new UploadedFile($this->makePng(), 'a.png', 'image/png', null, true);
        $action = app(ConvertImageToWebp::class);

        $a = $action($file, 'blog/webp', $this->disk);
        $b = $action($file, 'blog/webp', $this->disk);

        $this->assertNotSame($a['thumb'], $b['thumb']);
        $this->assertNotSame($a['full'], $b['full']);
    }

    public function test_delete_removesboth_paths(): void
    {
        $file = new UploadedFile($this->makePng(), 'a.png', 'image/png', null, true);
        $action = app(ConvertImageToWebp::class);
        $paths = $action($file, 'blog/webp', $this->disk);

        $this->assertTrue(Storage::disk($this->disk)->exists($paths['thumb']));
        $this->assertTrue(Storage::disk($this->disk)->exists($paths['full']));

        $action->delete($paths['thumb'], $paths['full'], $this->disk);

        $this->assertFalse(Storage::disk($this->disk)->exists($paths['thumb']));
        $this->assertFalse(Storage::disk($this->disk)->exists($paths['full']));
    }

    public function test_delete_is_noop_for_empty_paths(): void
    {
        $action = app(ConvertImageToWebp::class);

        $this->assertFalse($action->delete('', '', $this->disk));
    }

    public function test_accepts_custom_basename(): void
    {
        $file = new UploadedFile($this->makePng(), 'a.png', 'image/png', null, true);
        $action = app(ConvertImageToWebp::class);

        $paths = $action($file, 'blog/webp', $this->disk, 'custom-name');

        $this->assertStringContainsString('custom-name.webp', $paths['thumb']);
        $this->assertStringContainsString('custom-name.webp', $paths['full']);
    }

    public function test_defaults_to_ulid_when_basename_is_null(): void
    {
        $file = new UploadedFile($this->makePng(), 'a.png', 'image/png', null, true);
        $action = app(ConvertImageToWebp::class);

        $paths = $action($file, 'blog/webp', $this->disk);

        // ULID-based names should be 26 chars + .webp.
        $thumbFilename = basename($paths['thumb']);
        $this->assertMatchesRegularExpression('/^[0-9A-Z]{26}\.webp$/', $thumbFilename);
    }

    public function test_soft_delete_does_not_remove_image_files(): void
    {
        $author = User::factory()->admin()->create();
        $file = new UploadedFile($this->makePng(), 'a.png', 'image/png', null, true);
        $paths = app(ConvertImageToWebp::class)($file, 'blog/webp', $this->disk);

        $post = Post::factory()->create(['author_id' => $author->id]);
        $post->coverImage()->create([
            'thumb_path' => $paths['thumb'],
            'full_path' => $paths['full'],
            'order' => 0,
        ]);

        $post->delete();

        $this->assertTrue(Storage::disk($this->disk)->exists($paths['thumb']));
        $this->assertTrue(Storage::disk($this->disk)->exists($paths['full']));
    }

    public function test_force_delete_removes_image_files(): void
    {
        $author = User::factory()->admin()->create();
        $file = new UploadedFile($this->makePng(), 'a.png', 'image/png', null, true);
        $paths = app(ConvertImageToWebp::class)($file, 'blog/webp', $this->disk);

        $post = Post::factory()->create(['author_id' => $author->id]);
        $post->coverImage()->create([
            'thumb_path' => $paths['thumb'],
            'full_path' => $paths['full'],
            'order' => 0,
        ]);

        $post->forceDelete();

        $this->assertFalse(Storage::disk($this->disk)->exists($paths['thumb']));
        $this->assertFalse(Storage::disk($this->disk)->exists($paths['full']));
    }

    private function makePng(int $width = 800, int $height = 600): string
    {
        $path = tempnam(sys_get_temp_dir(), 'png_').'.png';
        $im = imagecreatetruecolor($width, $height);
        imagefill($im, 0, 0, imagecolorallocate($im, random_int(0, 255), random_int(0, 255), random_int(0, 255)));
        imagepng($im, $path);
        imagedestroy($im);

        return $path;
    }

    private function copyToTemp(string $relativePath): string
    {
        $tmp = tempnam(sys_get_temp_dir(), 'webp_').'.webp';
        file_put_contents($tmp, Storage::disk($this->disk)->get($relativePath));

        return $tmp;
    }
}
