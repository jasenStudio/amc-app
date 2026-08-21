<?php

namespace Tests\Unit;

use App\Models\Post;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TagTest extends TestCase
{
    use RefreshDatabase;

    public function test_slug_is_generated_from_name(): void
    {
        $tag = Tag::factory()->create(['name' => 'Hello World']);

        $this->assertSame('hello-world', $tag->slug);
    }

    public function test_slug_is_unique_among_tags(): void
    {
        Tag::factory()->create(['name' => 'Hello World']);
        $second = Tag::factory()->create(['name' => 'Hello World']);

        $this->assertSame('hello-world-1', $second->slug);
    }

    public function test_posts_relationship_returns_associated_posts(): void
    {
        $tag = Tag::factory()->create();
        $post = Post::factory()->create();
        $post->tags()->attach($tag);

        $this->assertCount(1, $tag->posts);
        $this->assertTrue($tag->posts->first()->is($post));
    }
}
