<?php

namespace Tests\Unit;

use App\Enums\PostStatus;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PostTest extends TestCase
{
    use RefreshDatabase;

    public function test_slug_is_generated_from_title_on_create(): void
    {
        $post = Post::factory()->create(['title' => 'Hello World']);

        $this->assertSame('hello-world', $post->slug);
    }

    public function test_slug_is_unique_when_title_collides(): void
    {
        Post::factory()->create(['title' => 'Hello World']);

        $second = Post::factory()->create(['title' => 'Hello World']);

        $this->assertSame('hello-world', Post::first()->slug);
        $this->assertSame('hello-world-1', $second->slug);
    }

    public function test_slug_is_regenerated_when_title_changes(): void
    {
        $post = Post::factory()->create(['title' => 'Original']);

        $post->update(['title' => 'Updated']);

        $this->assertSame('updated', $post->fresh()->slug);
    }

    public function test_slug_can_be_preserved_when_only_other_attributes_change(): void
    {
        $post = Post::factory()->create(['title' => 'Original']);

        $post->update(['excerpt' => 'New excerpt']);

        $this->assertSame('original', $post->fresh()->slug);
    }

    public function test_published_scope_returns_only_published_posts_in_window(): void
    {
        Post::factory()->published()->create();
        Post::factory()->scheduled()->create();
        Post::factory()->create(['status' => PostStatus::Draft]);

        $this->assertCount(1, Post::published()->get());
    }

    public function test_published_scope_excludes_scheduled_posts(): void
    {
        Post::factory()->scheduled()->create();

        $this->assertCount(0, Post::published()->get());
    }

    public function test_featured_scope_returns_only_featured_posts(): void
    {
        Post::factory()->featured()->create();
        Post::factory()->create();

        $this->assertCount(1, Post::featured()->get());
    }

    public function test_ordered_scope_sorts_by_order_then_published_at(): void
    {
        $old = Post::factory()->published(now()->subDays(5))->create(['order' => 1]);
        $new = Post::factory()->published(now())->create(['order' => 1]);
        $top = Post::factory()->create(['order' => 0]);

        $ids = Post::ordered()->pluck('id')->all();

        $this->assertSame([$top->id, $new->id, $old->id], $ids);
    }

    public function test_soft_delete_excludes_post_from_default_query(): void
    {
        $post = Post::factory()->create();
        $post->delete();

        $this->assertNull(Post::find($post->id));
        $this->assertNotNull(Post::withTrashed()->find($post->id));
    }

    public function test_seo_title_falls_back_to_title(): void
    {
        $post = Post::factory()->create(['title' => 'Visible', 'seo_title' => null]);

        $this->assertSame('Visible', $post->seoTitle());
    }

    public function test_seo_description_falls_back_to_excerpt(): void
    {
        $post = Post::factory()->create(['excerpt' => 'Resumen', 'seo_description' => null]);

        $this->assertSame('Resumen', $post->seoDescription());
    }

    public function test_seo_image_falls_back_to_cover_image(): void
    {
        $post = Post::factory()->create(['seo_image' => null]);
        $post->coverImage()->create([
            'thumb_path' => 'blog/webp/thumbs/cover.webp',
            'full_path' => 'blog/webp/full/cover.webp',
            'order' => 0,
        ]);

        $this->assertSame('blog/webp/full/cover.webp', $post->seoImage());
    }

    public function test_cover_image_url_accessors_resolve_from_relationship(): void
    {
        $post = Post::factory()->create();
        $post->coverImage()->create([
            'thumb_path' => 'blog/webp/thumbs/cover.webp',
            'full_path' => 'blog/webp/full/cover.webp',
            'order' => 0,
        ]);

        $this->assertSame(
            Storage::disk('public')->url('blog/webp/full/cover.webp'),
            $post->cover_image_url,
        );
        $this->assertSame(
            Storage::disk('public')->url('blog/webp/thumbs/cover.webp'),
            $post->cover_image_thumb_url,
        );
    }

    public function test_cover_image_url_accessors_are_null_without_image(): void
    {
        $post = Post::factory()->create();

        $this->assertNull($post->cover_image_url);
        $this->assertNull($post->cover_image_thumb_url);
    }

    public function test_author_relationship_returns_user(): void
    {
        $author = User::factory()->admin()->create();
        $post = Post::factory()->create(['author_id' => $author->id]);

        $this->assertTrue($post->author->is($author));
    }

    public function test_tags_relationship_persists_pivot(): void
    {
        $post = Post::factory()->create();
        $tags = Tag::factory()->count(2)->create();

        $post->tags()->sync($tags->pluck('id'));

        $this->assertSame(2, $post->tags()->count());
    }
}
