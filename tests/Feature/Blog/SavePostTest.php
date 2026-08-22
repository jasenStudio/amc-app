<?php

namespace Tests\Feature\Blog;

use App\Actions\Blog\SavePost;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use App\Support\HtmlSanitizer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use InvalidArgumentException;
use Tests\TestCase;

class SavePostTest extends TestCase
{
    use RefreshDatabase;

    private SavePost $action;

    protected function setUp(): void
    {
        parent::setUp();
        $this->action = new SavePost(new HtmlSanitizer);
    }

    public function test_creates_post_with_sanitized_body(): void
    {
        $author = User::factory()->admin()->create();

        $post = $this->action->handle(
            post: null,
            data: [
                'title' => 'Test Post',
                'slug' => 'test-post',
                'body' => '<p>safe</p><script>evil()</script>',
                'status' => 'draft',
                'excerpt' => null,
                'published_at' => null,
                'featured' => false,
                'order' => 0,
                'seo_title' => null,
                'seo_description' => null,
                'seo_image' => null,
            ],
            authorId: $author->id,
            tagIds: [],
            newTagName: null,
            shouldRemoveCover: false,
            coverThumbPath: null,
            coverFullPath: null,
        );

        $this->assertNotNull($post->id);
        $this->assertSame($author->id, $post->author_id);
        $this->assertStringNotContainsString('<script', $post->body);
        $this->assertStringContainsString('<p>safe</p>', $post->body);
    }

    public function test_updates_existing_post(): void
    {
        $author = User::factory()->admin()->create();
        $post = Post::factory()->create(['author_id' => $author->id, 'title' => 'Old']);

        $result = $this->action->handle(
            post: $post,
            data: [
                'title' => 'New',
                'slug' => $post->slug,
                'body' => '<p>updated</p>',
                'status' => 'draft',
                'excerpt' => null,
                'published_at' => null,
                'featured' => false,
                'order' => 0,
                'seo_title' => null,
                'seo_description' => null,
                'seo_image' => null,
            ],
            authorId: $author->id,
            tagIds: [],
            newTagName: null,
            shouldRemoveCover: false,
            coverThumbPath: null,
            coverFullPath: null,
        );

        $this->assertSame('New', $result->fresh()->title);
    }

    public function test_throws_when_body_is_empty_after_sanitization(): void
    {
        $author = User::factory()->admin()->create();

        $this->expectException(InvalidArgumentException::class);

        $this->action->handle(
            post: null,
            data: [
                'title' => 'Test',
                'slug' => 'test',
                'body' => '<script></script>',
                'status' => 'draft',
                'excerpt' => null,
                'published_at' => null,
                'featured' => false,
                'order' => 0,
                'seo_title' => null,
                'seo_description' => null,
                'seo_image' => null,
            ],
            authorId: $author->id,
            tagIds: [],
            newTagName: null,
            shouldRemoveCover: false,
            coverThumbPath: null,
            coverFullPath: null,
        );
    }

    public function test_creates_new_tag_from_name(): void
    {
        $author = User::factory()->admin()->create();

        $post = $this->action->handle(
            post: null,
            data: [
                'title' => 'Test',
                'slug' => 'test',
                'body' => '<p>body</p>',
                'status' => 'draft',
                'excerpt' => null,
                'published_at' => null,
                'featured' => false,
                'order' => 0,
                'seo_title' => null,
                'seo_description' => null,
                'seo_image' => null,
            ],
            authorId: $author->id,
            tagIds: [],
            newTagName: 'Brand New',
            shouldRemoveCover: false,
            coverThumbPath: null,
            coverFullPath: null,
        );

        $this->assertDatabaseHas('tags', ['name' => 'Brand New', 'slug' => 'brand-new']);
        $this->assertTrue($post->tags->contains(Tag::where('slug', 'brand-new')->first()));
    }

    public function test_syncs_tags(): void
    {
        $author = User::factory()->admin()->create();
        $post = Post::factory()->create(['author_id' => $author->id]);
        $oldTag = Tag::factory()->create();
        $newTag = Tag::factory()->create();
        $post->tags()->attach($oldTag);

        $this->action->handle(
            post: $post,
            data: [
                'title' => $post->title,
                'slug' => $post->slug,
                'body' => '<p>body</p>',
                'status' => 'draft',
                'excerpt' => null,
                'published_at' => null,
                'featured' => false,
                'order' => 0,
                'seo_title' => null,
                'seo_description' => null,
                'seo_image' => null,
            ],
            authorId: $author->id,
            tagIds: [$newTag->id],
            newTagName: null,
            shouldRemoveCover: false,
            coverThumbPath: null,
            coverFullPath: null,
        );

        $this->assertFalse($post->fresh()->tags->contains($oldTag));
        $this->assertTrue($post->fresh()->tags->contains($newTag));
    }

    public function test_removes_cover_image_when_flag_is_set(): void
    {
        $author = User::factory()->admin()->create();
        $post = Post::factory()->create(['author_id' => $author->id]);
        $post->coverImage()->create([
            'thumb_path' => 'thumbs/test.webp',
            'full_path' => 'full/test.webp',
            'order' => 0,
        ]);

        $this->assertNotNull($post->fresh()->coverImage);

        $this->action->handle(
            post: $post,
            data: [
                'title' => $post->title,
                'slug' => $post->slug,
                'body' => '<p>body</p>',
                'status' => 'draft',
                'excerpt' => null,
                'published_at' => null,
                'featured' => false,
                'order' => 0,
                'seo_title' => null,
                'seo_description' => null,
                'seo_image' => null,
            ],
            authorId: $author->id,
            tagIds: [],
            newTagName: null,
            shouldRemoveCover: true,
            coverThumbPath: null,
            coverFullPath: null,
        );

        $this->assertNull($post->fresh()->coverImage);
    }

    public function test_sets_cover_image_when_paths_provided(): void
    {
        $author = User::factory()->admin()->create();

        $post = $this->action->handle(
            post: null,
            data: [
                'title' => 'Covered',
                'slug' => 'covered',
                'body' => '<p>body</p>',
                'status' => 'draft',
                'excerpt' => null,
                'published_at' => null,
                'featured' => false,
                'order' => 0,
                'seo_title' => null,
                'seo_description' => null,
                'seo_image' => null,
            ],
            authorId: $author->id,
            tagIds: [],
            newTagName: null,
            shouldRemoveCover: false,
            coverThumbPath: 'thumbs/cover.webp',
            coverFullPath: 'full/cover.webp',
        );

        $this->assertNotNull($post->coverImage);
        $this->assertSame('thumbs/cover.webp', $post->coverImage->thumb_path);
        $this->assertSame('full/cover.webp', $post->coverImage->full_path);
    }
}
