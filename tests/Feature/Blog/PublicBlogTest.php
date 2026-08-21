<?php

namespace Tests\Feature\Blog;

use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicBlogTest extends TestCase
{
    use RefreshDatabase;

    public function test_blog_index_is_accessible_publicly(): void
    {
        $this->get(route('blog'))->assertOk();
    }

    public function test_blog_index_only_shows_published_posts(): void
    {
        $admin = User::factory()->admin()->create();
        Post::factory()->published()->create(['author_id' => $admin->id, 'title' => 'Published Post']);
        Post::factory()->create(['author_id' => $admin->id, 'title' => 'Draft Post']);

        $this->get(route('blog'))
            ->assertSee('Published Post')
            ->assertDontSee('Draft Post');
    }

    public function test_blog_index_excludes_future_published_posts(): void
    {
        $admin = User::factory()->admin()->create();
        Post::factory()->scheduled()->create(['author_id' => $admin->id, 'title' => 'Future Post']);

        $this->get(route('blog'))
            ->assertDontSee('Future Post');
    }

    public function test_blog_index_filters_by_tag(): void
    {
        $admin = User::factory()->admin()->create();
        $tagA = Tag::factory()->create(['name' => 'Laravel']);
        $tagB = Tag::factory()->create(['name' => 'Vue']);
        $p1 = Post::factory()->published()->create(['author_id' => $admin->id, 'title' => 'Laravel Post']);
        $p1->tags()->attach($tagA);
        $p2 = Post::factory()->published()->create(['author_id' => $admin->id, 'title' => 'Vue Post']);
        $p2->tags()->attach($tagB);

        $this->get(route('blog', ['tag' => $tagA->slug]))
            ->assertSee('Laravel Post')
            ->assertDontSee('Vue Post');
    }

    public function test_blog_show_is_accessible_by_slug(): void
    {
        $admin = User::factory()->admin()->create();
        $post = Post::factory()->published()->create(['author_id' => $admin->id, 'title' => 'My Post', 'slug' => 'my-post']);

        $this->get(route('blog.show', 'my-post'))
            ->assertOk()
            ->assertSee('My Post');
    }

    public function test_blog_show_returns_404_for_draft(): void
    {
        $admin = User::factory()->admin()->create();
        Post::factory()->create(['author_id' => $admin->id, 'slug' => 'draft-post']);

        $this->get(route('blog.show', 'draft-post'))
            ->assertNotFound();
    }

    public function test_blog_show_returns_404_for_nonexistent_slug(): void
    {
        $this->get(route('blog.show', 'nonexistent'))
            ->assertNotFound();
    }

    public function test_blog_show_displays_seo_meta(): void
    {
        $admin = User::factory()->admin()->create();
        $post = Post::factory()->published()->create([
            'author_id' => $admin->id,
            'title' => 'Visible Title',
            'seo_title' => 'Custom SEO',
            'seo_description' => 'Custom desc',
        ]);

        $this->get(route('blog.show', $post->slug))
            ->assertSee('Custom SEO')
            ->assertSee('Custom desc');
    }

    public function test_blog_show_seo_falls_back_to_title_and_excerpt(): void
    {
        $admin = User::factory()->admin()->create();
        $post = Post::factory()->published()->create([
            'author_id' => $admin->id,
            'title' => 'Post Title',
            'excerpt' => 'Post excerpt',
            'seo_title' => null,
            'seo_description' => null,
        ]);

        $this->get(route('blog.show', $post->slug))
            ->assertSee('Post Title')
            ->assertSee('Post excerpt');
    }

    public function test_home_page_shows_featured_posts(): void
    {
        $admin = User::factory()->admin()->create();
        Post::factory()->published()->featured()->create(['author_id' => $admin->id, 'title' => 'Featured']);
        Post::factory()->published()->create(['author_id' => $admin->id, 'title' => 'Not Featured']);

        $this->get(route('home'))
            ->assertSee('Featured')
            ->assertDontSee('Not Featured');
    }
}
