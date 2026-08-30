<?php

namespace Tests\Feature\Blog;

use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomeBlogSectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_hides_blog_section_when_no_featured_posts(): void
    {
        Post::factory()->published()->create(['featured' => false]);

        $this->get(route('home'))
            ->assertOk()
            ->assertDontSee('id="blog"', false)
            ->assertDontSee('Ir al blog', false);
    }

    public function test_home_shows_blog_section_when_featured_posts_exist(): void
    {
        Post::factory()->published()->featured()->create(['title' => 'Featured Post']);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('id="blog"', false)
            ->assertSee('Ir al blog', false)
            ->assertSee('Featured Post');
    }
}
