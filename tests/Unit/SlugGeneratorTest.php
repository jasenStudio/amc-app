<?php

namespace Tests\Unit;

use App\Models\Post;
use App\Support\SlugGenerator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SlugGeneratorTest extends TestCase
{
    use RefreshDatabase;

    public function test_generates_slug_from_title(): void
    {
        $slug = SlugGenerator::unique(Post::class, 'Hello World');

        $this->assertSame('hello-world', $slug);
    }

    public function test_falls_back_to_n_a_when_slug_is_empty(): void
    {
        $slug = SlugGenerator::unique(Post::class, '!!!');

        $this->assertSame('n-a', $slug);
    }

    public function test_appends_suffix_when_slug_already_exists(): void
    {
        Post::factory()->create(['title' => 'Hello World', 'slug' => 'hello-world']);

        $slug = SlugGenerator::unique(Post::class, 'Hello World');

        $this->assertSame('hello-world-1', $slug);
    }

    public function test_increments_suffix_until_unique(): void
    {
        $p1 = Post::factory()->create(['title' => 'One']);
        Post::where('id', $p1->id)->update(['slug' => 'hello-world']);
        $p2 = Post::factory()->create(['title' => 'Two']);
        Post::where('id', $p2->id)->update(['slug' => 'hello-world-1']);
        $p3 = Post::factory()->create(['title' => 'Three']);
        Post::where('id', $p3->id)->update(['slug' => 'hello-world-2']);

        $slug = SlugGenerator::unique(Post::class, 'Hello World');

        $this->assertSame('hello-world-3', $slug);
    }

    public function test_excludes_own_record_by_ignore_id(): void
    {
        $post = Post::factory()->create(['slug' => 'hello-world']);

        $slug = SlugGenerator::unique(Post::class, 'Hello World', $post->id);

        $this->assertSame('hello-world', $slug);
    }

    public function test_includes_soft_deleted_records(): void
    {
        $post = Post::factory()->create(['title' => 'Hello World']);
        $post->delete();

        $slug = SlugGenerator::unique(Post::class, 'Hello World');

        $this->assertSame('hello-world-1', $slug);
    }
}
