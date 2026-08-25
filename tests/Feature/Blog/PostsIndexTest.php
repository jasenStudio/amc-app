<?php

namespace Tests\Feature\Blog;

use App\Enums\PostStatus;
use App\Livewire\Blog\PostsIndex;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PostsIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get(route('blog.index'))
            ->assertRedirect(route('login'));
    }

    public function test_pending_user_is_redirected_to_pending_approval(): void
    {
        $pending = User::factory()->pending()->create();

        $this->actingAs($pending)
            ->get(route('blog.index'))
            ->assertRedirect(route('pending.approval'));
    }

    public function test_admin_can_view_index(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('blog.index'))
            ->assertOk();
    }

    public function test_editor_can_view_index(): void
    {
        $editor = User::factory()->editor()->create();

        $this->actingAs($editor)
            ->get(route('blog.index'))
            ->assertOk();
    }

    public function test_admin_sees_all_posts(): void
    {
        $admin = User::factory()->admin()->create();
        $editor = User::factory()->editor()->create();
        Post::factory()->count(2)->create(['author_id' => $editor->id]);
        Post::factory()->count(2)->create(['author_id' => $admin->id]);

        Livewire::actingAs($admin)
            ->test(PostsIndex::class)
            ->assertViewHas('posts', fn ($posts) => $posts->total() === 4);
    }

    public function test_search_filters_by_title(): void
    {
        $admin = User::factory()->admin()->create();
        Post::factory()->create(['title' => 'Laravel Tips', 'author_id' => $admin->id]);
        Post::factory()->create(['title' => 'Other Post', 'author_id' => $admin->id]);

        Livewire::actingAs($admin)
            ->test(PostsIndex::class)
            ->set('search', 'Laravel')
            ->assertViewHas('posts', fn ($posts) => $posts->total() === 1);
    }

    public function test_status_filter(): void
    {
        $admin = User::factory()->admin()->create();
        Post::factory()->count(2)->published()->create(['author_id' => $admin->id]);
        Post::factory()->count(3)->create(['author_id' => $admin->id, 'status' => PostStatus::Draft]);

        Livewire::actingAs($admin)
            ->test(PostsIndex::class)
            ->set('status', 'published')
            ->assertViewHas('posts', fn ($posts) => $posts->total() === 2)
            ->set('status', 'draft')
            ->assertViewHas('posts', fn ($posts) => $posts->total() === 3);
    }

    public function test_featured_filter(): void
    {
        $admin = User::factory()->admin()->create();
        Post::factory()->count(2)->featured()->create(['author_id' => $admin->id]);
        Post::factory()->count(3)->create(['author_id' => $admin->id]);

        Livewire::actingAs($admin)
            ->test(PostsIndex::class)
            ->set('featured', 'yes')
            ->assertViewHas('posts', fn ($posts) => $posts->total() === 2);
    }

    public function test_tag_filter(): void
    {
        $admin = User::factory()->admin()->create();
        $tagA = Tag::factory()->create(['name' => 'Laravel']);
        $tagB = Tag::factory()->create(['name' => 'Vue']);

        $p1 = Post::factory()->create(['author_id' => $admin->id]);
        $p1->tags()->attach($tagA);
        $p2 = Post::factory()->create(['author_id' => $admin->id]);
        $p2->tags()->attach($tagB);

        Livewire::actingAs($admin)
            ->test(PostsIndex::class)
            ->set('tag', $tagA->slug)
            ->assertViewHas('posts', fn ($posts) => $posts->total() === 1);
    }

    public function test_reset_filters(): void
    {
        $admin = User::factory()->admin()->create();
        Post::factory()->create(['title' => 'Foo', 'author_id' => $admin->id]);
        Post::factory()->count(5)->create(['author_id' => $admin->id]);

        Livewire::actingAs($admin)
            ->test(PostsIndex::class)
            ->set('search', 'Foo')
            ->assertViewHas('posts', fn ($posts) => $posts->total() === 1)
            ->call('resetFilters')
            ->assertViewHas('posts', fn ($posts) => $posts->total() === 6);
    }

    public function test_admin_can_soft_delete_any_post(): void
    {
        $admin = User::factory()->admin()->create();
        $editor = User::factory()->editor()->create();
        $post = Post::factory()->create(['author_id' => $editor->id]);

        Livewire::actingAs($admin)
            ->test(PostsIndex::class)
            ->call('delete', $post->id);

        $this->assertSoftDeleted('posts', ['id' => $post->id]);
    }

    public function test_editor_can_soft_delete_their_own_post(): void
    {
        $editor = User::factory()->editor()->create();
        $post = Post::factory()->create(['author_id' => $editor->id]);

        Livewire::actingAs($editor)
            ->test(PostsIndex::class)
            ->call('delete', $post->id);

        $this->assertSoftDeleted('posts', ['id' => $post->id]);
    }

    public function test_editor_cannot_soft_delete_another_editors_post(): void
    {
        $editor = User::factory()->editor()->create();
        $otherEditor = User::factory()->editor()->create();
        $post = Post::factory()->create(['author_id' => $otherEditor->id]);

        Livewire::actingAs($editor)
            ->test(PostsIndex::class)
            ->call('delete', $post->id);

        $this->assertNotSoftDeleted('posts', ['id' => $post->id]);
    }

    public function test_user_without_role_cannot_soft_delete_any_post(): void
    {
        $user = User::factory()->create();
        $admin = User::factory()->admin()->create();
        $post = Post::factory()->create(['author_id' => $admin->id]);

        Livewire::actingAs($user)
            ->test(PostsIndex::class)
            ->call('delete', $post->id);

        $this->assertNotSoftDeleted('posts', ['id' => $post->id]);
    }

    public function test_editor_sees_only_their_own_posts(): void
    {
        $admin = User::factory()->admin()->create();
        $editor = User::factory()->editor()->create();
        $otherEditor = User::factory()->editor()->create();

        Post::factory()->count(2)->create(['author_id' => $editor->id]);
        Post::factory()->create(['author_id' => $otherEditor->id]);
        Post::factory()->create(['author_id' => $admin->id]);

        Livewire::actingAs($editor)
            ->test(PostsIndex::class)
            ->assertViewHas('posts', fn ($posts) => $posts->total() === 2);
    }

    public function test_view_only_renders_buttons_for_posts_user_can_act_on(): void
    {
        $admin = User::factory()->admin()->create();
        $editor = User::factory()->editor()->create();
        $otherEditor = User::factory()->editor()->create();
        $ownPost = Post::factory()->create(['author_id' => $editor->id, 'title' => 'Own Post']);
        $othersPost = Post::factory()->create(['author_id' => $otherEditor->id, 'title' => 'Other Post']);

        Livewire::actingAs($editor)
            ->test(PostsIndex::class)
            ->assertSee('Own Post')
            ->assertDontSee('Other Post')
            ->assertSeeHtml("data-test=\"edit-post-{$ownPost->id}\"")
            ->assertSeeHtml("data-test=\"delete-post-{$ownPost->id}\"");
    }

    public function test_admin_sees_buttons_for_all_posts(): void
    {
        $admin = User::factory()->admin()->create();
        $editor = User::factory()->editor()->create();
        $post = Post::factory()->create(['author_id' => $editor->id]);

        Livewire::actingAs($admin)
            ->test(PostsIndex::class)
            ->assertSeeHtml("data-test=\"edit-post-{$post->id}\"")
            ->assertSeeHtml("data-test=\"delete-post-{$post->id}\"");
    }

    public function test_post_with_soft_deleted_author_still_shows_author_name(): void
    {
        $admin = User::factory()->admin()->create();
        $author = User::factory()->editor()->create(['name' => 'Deleted Author']);
        $post = Post::factory()->create(['author_id' => $author->id, 'title' => 'Old Post']);
        $author->delete();

        Livewire::actingAs($admin)
            ->test(PostsIndex::class)
            ->assertSee('Deleted Author')
            ->assertSee('Old Post');
    }
}
