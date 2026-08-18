<?php

namespace Tests\Unit;

use App\Actions\Images\UploadImageAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Tests\TestCase;

class UploadImageActionTest extends TestCase
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
        $file = new UploadedFile($this->makePng(), 'cover.png', 'image/png', null, true);

        $action = app(UploadImageAction::class);
        $paths = $action($file, 'blog/webp', 'my-post', $this->disk);

        $this->assertStringStartsWith('blog/webp/thumbs/', $paths['thumb']);
        $this->assertStringEndsWith('.webp', $paths['thumb']);
        $this->assertStringStartsWith('blog/webp/full/', $paths['full']);
        $this->assertStringEndsWith('.webp', $paths['full']);
        $this->assertSame('image/webp', Storage::disk($this->disk)->mimeType($paths['thumb']));
        $this->assertSame('image/webp', Storage::disk($this->disk)->mimeType($paths['full']));
    }

    public function test_generates_semantic_name_when_slug_hint_provided(): void
    {
        $file = new UploadedFile($this->makePng(), 'cover.png', 'image/png', null, true);

        $action = app(UploadImageAction::class);
        $paths = $action($file, 'blog/webp', 'Mi Post Seguro', $this->disk);

        // Filename should contain slug and a short identifier.
        $thumbFilename = basename($paths['thumb']);
        $this->assertMatchesRegularExpression('/^mi-post-seguro-[a-zA-Z0-9]{8}\.webp$/', $thumbFilename);
    }

    public function test_generates_generic_name_when_no_slug_hint(): void
    {
        $file = new UploadedFile($this->makePng(), 'cover.png', 'image/png', null, true);

        $action = app(UploadImageAction::class);
        $paths = $action($file, 'blog/webp/body', null, $this->disk);

        $fullFilename = basename($paths['full']);
        $this->assertMatchesRegularExpression('/^image-[a-zA-Z0-9]{8}\.webp$/', $fullFilename);
    }

    public function test_generates_generic_name_when_empty_slug_hint(): void
    {
        $file = new UploadedFile($this->makePng(), 'cover.png', 'image/png', null, true);

        $action = app(UploadImageAction::class);
        $paths = $action($file, 'blog/webp/body', '', $this->disk);

        $fullFilename = basename($paths['full']);
        $this->assertMatchesRegularExpression('/^image-[a-zA-Z0-9]{8}\.webp$/', $fullFilename);
    }

    public function test_basename_is_unique_across_calls(): void
    {
        $file = new UploadedFile($this->makePng(), 'a.png', 'image/png', null, true);
        $action = app(UploadImageAction::class);

        $a = $action($file, 'blog/webp', 'post', $this->disk);
        $b = $action($file, 'blog/webp', 'post', $this->disk);

        $this->assertNotSame($a['thumb'], $b['thumb']);
        $this->assertNotSame($a['full'], $b['full']);
    }

    public function test_rejects_file_larger_than_2mb(): void
    {
        // Create a file that reports 3 MB.
        $file = UploadedFile::fake()->image('big.png', 800, 600)->size(3072);

        $action = app(UploadImageAction::class);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('exceeds the 2048 KB limit');

        $action($file, 'blog/webp', 'test', $this->disk);
    }

    public function test_rejects_file_with_invalid_mime(): void
    {
        $file = UploadedFile::fake()->create('document.pdf', 100, 'application/pdf');

        $action = app(UploadImageAction::class);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('not permitted');

        $action($file, 'blog/webp', 'test', $this->disk);
    }

    public function test_rejects_file_with_invalid_dimensions(): void
    {
        // Fake image with 5000x5000 dimensions.
        $file = UploadedFile::fake()->image('huge.png', 5000, 5000)->size(100);

        $action = app(UploadImageAction::class);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('exceed the maximum allowed');

        $action($file, 'blog/webp', 'test', $this->disk);
    }

    public function test_produces_thumb_at_640px_and_full_at_1600px(): void
    {
        $source = $this->makePng(width: 2400, height: 1600);
        $file = new UploadedFile($source, 'cover.png', 'image/png', null, true);

        $action = app(UploadImageAction::class);
        $paths = $action($file, 'blog/webp', 'test', $this->disk);

        $tmpThumb = $this->copyToTemp($paths['thumb']);
        $tmpFull = $this->copyToTemp($paths['full']);

        $thumbSize = getimagesize($tmpThumb);
        $fullSize = getimagesize($tmpFull);

        $this->assertSame(640, $thumbSize[0]);
        $this->assertSame(1600, $fullSize[0]);
        $this->assertSame('image/webp', $thumbSize['mime']);
        $this->assertSame('image/webp', $fullSize['mime']);
    }

    public function test_stored_as_webp(): void
    {
        $file = new UploadedFile($this->makePng(), 'cover.png', 'image/png', null, true);

        $action = app(UploadImageAction::class);
        $paths = $action($file, 'blog/webp', 'test', $this->disk);

        $this->assertSame('image/webp', Storage::disk($this->disk)->mimeType($paths['thumb']));
        $this->assertSame('image/webp', Storage::disk($this->disk)->mimeType($paths['full']));
    }

    public function test_path_belongs_to_expected_directory(): void
    {
        $file = new UploadedFile($this->makePng(), 'cover.png', 'image/png', null, true);

        $action = app(UploadImageAction::class);
        $paths = $action($file, 'blog/webp', 'test', $this->disk);

        $this->assertStringStartsWith('blog/webp/thumbs/', $paths['thumb']);
        $this->assertStringStartsWith('blog/webp/full/', $paths['full']);
    }

    public function test_inline_path_uses_body_subdirectory(): void
    {
        $file = new UploadedFile($this->makePng(), 'img.png', 'image/png', null, true);

        $action = app(UploadImageAction::class);
        $paths = $action($file, 'blog/webp/body', 'inline', $this->disk);

        $this->assertStringStartsWith('blog/webp/body/thumbs/', $paths['thumb']);
        $this->assertStringStartsWith('blog/webp/body/full/', $paths['full']);
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
