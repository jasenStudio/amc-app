<?php

namespace Tests\Feature\Blog;

use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CleanupOrphanImagesTest extends TestCase
{
    use RefreshDatabase;

    private string $disk = 'public';

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake($this->disk);
    }

    public function test_deletes_files_not_referenced_anywhere(): void
    {
        Storage::disk($this->disk)->put('blog/webp/full/orphan.webp', 'x');
        Storage::disk($this->disk)->put('blog/webp/thumbs/orphan.webp', 'x');

        $this->artisan('blog:cleanup-orphan-images')
            ->expectsOutputToContain('2 orphan file(s) deleted.');

        Storage::disk($this->disk)->assertMissing('blog/webp/full/orphan.webp');
        Storage::disk($this->disk)->assertMissing('blog/webp/thumbs/orphan.webp');
    }

    public function test_keeps_files_referenced_by_image_record(): void
    {
        $post = Post::factory()->create();
        $post->coverImage()->create([
            'thumb_path' => 'blog/webp/thumbs/kept.webp',
            'full_path' => 'blog/webp/full/kept.webp',
            'order' => 0,
        ]);

        Storage::disk($this->disk)->put('blog/webp/full/kept.webp', 'x');
        Storage::disk($this->disk)->put('blog/webp/thumbs/kept.webp', 'x');
        Storage::disk($this->disk)->put('blog/webp/full/orphan.webp', 'x');

        $this->artisan('blog:cleanup-orphan-images')
            ->expectsOutputToContain('1 orphan file(s) deleted.');

        Storage::disk($this->disk)->assertExists('blog/webp/full/kept.webp');
        Storage::disk($this->disk)->assertExists('blog/webp/thumbs/kept.webp');
        Storage::disk($this->disk)->assertMissing('blog/webp/full/orphan.webp');
    }

    public function test_keeps_files_referenced_in_post_body(): void
    {
        Post::factory()->create([
            'body' => '<p><img src="/storage/blog/webp/body/inline.webp" alt="x"></p>',
        ]);

        Storage::disk($this->disk)->put('blog/webp/body/inline.webp', 'x');
        Storage::disk($this->disk)->put('blog/webp/full/orphan.webp', 'x');

        $this->artisan('blog:cleanup-orphan-images')
            ->expectsOutputToContain('1 orphan file(s) deleted.');

        Storage::disk($this->disk)->assertExists('blog/webp/body/inline.webp');
        Storage::disk($this->disk)->assertMissing('blog/webp/full/orphan.webp');
    }

    public function test_keeps_files_referenced_by_seo_image(): void
    {
        Post::factory()->create(['seo_image' => 'blog/webp/full/seo.webp']);

        Storage::disk($this->disk)->put('blog/webp/full/seo.webp', 'x');
        Storage::disk($this->disk)->put('blog/webp/thumbs/orphan.webp', 'x');

        $this->artisan('blog:cleanup-orphan-images');

        Storage::disk($this->disk)->assertExists('blog/webp/full/seo.webp');
        Storage::disk($this->disk)->assertMissing('blog/webp/thumbs/orphan.webp');
    }

    public function test_dry_run_lists_files_without_deleting(): void
    {
        Storage::disk($this->disk)->put('blog/webp/full/orphan.webp', 'x');

        $this->artisan('blog:cleanup-orphan-images', ['--dry-run' => true])
            ->expectsOutputToContain('Would delete: blog/webp/full/orphan.webp')
            ->expectsOutputToContain('1 orphan file(s) would be deleted.');

        Storage::disk($this->disk)->assertExists('blog/webp/full/orphan.webp');
    }
}
