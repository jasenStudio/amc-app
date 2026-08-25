<?php

namespace Tests\Unit;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostPolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_can_update_any_post(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();
        $author = User::factory()->editor()->create();
        $post = Post::factory()->create(['author_id' => $author->id]);

        $this->assertTrue($superAdmin->can('update', $post));
    }

    public function test_admin_can_update_any_post(): void
    {
        $admin = User::factory()->admin()->create();
        $author = User::factory()->editor()->create();
        $post = Post::factory()->create(['author_id' => $author->id]);

        $this->assertTrue($admin->can('update', $post));
    }

    public function test_editor_can_update_their_own_post(): void
    {
        $editor = User::factory()->editor()->create();
        $post = Post::factory()->create(['author_id' => $editor->id]);

        $this->assertTrue($editor->can('update', $post));
    }

    public function test_editor_cannot_update_another_editors_post(): void
    {
        $editor = User::factory()->editor()->create();
        $otherEditor = User::factory()->editor()->create();
        $post = Post::factory()->create(['author_id' => $otherEditor->id]);

        $this->assertFalse($editor->can('update', $post));
    }

    public function test_pending_user_cannot_update_any_post(): void
    {
        $pending = User::factory()->pending()->create();
        $otherUser = User::factory()->editor()->create();
        $post = Post::factory()->create(['author_id' => $otherUser->id]);

        $this->assertFalse($pending->can('update', $post));
    }

    public function test_pending_user_cannot_update_their_own_post_either(): void
    {
        $pending = User::factory()->pending()->create();
        $post = Post::factory()->create(['author_id' => $pending->id]);

        $this->assertFalse($pending->can('update', $post));
    }

    public function test_super_admin_can_delete_any_post(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();
        $author = User::factory()->editor()->create();
        $post = Post::factory()->create(['author_id' => $author->id]);

        $this->assertTrue($superAdmin->can('delete', $post));
    }

    public function test_admin_can_delete_any_post(): void
    {
        $admin = User::factory()->admin()->create();
        $author = User::factory()->editor()->create();
        $post = Post::factory()->create(['author_id' => $author->id]);

        $this->assertTrue($admin->can('delete', $post));
    }

    public function test_editor_can_delete_their_own_post(): void
    {
        $editor = User::factory()->editor()->create();
        $post = Post::factory()->create(['author_id' => $editor->id]);

        $this->assertTrue($editor->can('delete', $post));
    }

    public function test_editor_cannot_delete_another_editors_post(): void
    {
        $editor = User::factory()->editor()->create();
        $otherEditor = User::factory()->editor()->create();
        $post = Post::factory()->create(['author_id' => $otherEditor->id]);

        $this->assertFalse($editor->can('delete', $post));
    }

    public function test_restore_follows_same_criteria_as_delete(): void
    {
        $editor = User::factory()->editor()->create();
        $otherEditor = User::factory()->editor()->create();
        $post = Post::factory()->create(['author_id' => $otherEditor->id]);

        $this->assertFalse($editor->can('restore', $post));
        $this->assertTrue($otherEditor->can('restore', $post));
    }

    public function test_super_admin_and_admin_can_force_delete(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();
        $admin = User::factory()->admin()->create();
        $editor = User::factory()->editor()->create();
        $post = Post::factory()->create(['author_id' => $editor->id]);

        $this->assertTrue($superAdmin->can('forceDelete', $post));
        $this->assertTrue($admin->can('forceDelete', $post));
        $this->assertFalse($editor->can('forceDelete', $post));
    }

    public function test_create_requires_manage_posts_gate(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();
        $admin = User::factory()->admin()->create();
        $editor = User::factory()->editor()->create();
        $pending = User::factory()->pending()->create();

        $this->assertTrue($superAdmin->can('create', Post::class));
        $this->assertTrue($admin->can('create', Post::class));
        $this->assertTrue($editor->can('create', Post::class));
        $this->assertFalse($pending->can('create', Post::class));
    }
}
