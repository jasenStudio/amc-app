<?php

namespace Tests\Feature\Blog;

use App\Livewire\Blog\CommentForm;
use App\Models\CommentPrivacyConsent;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CommentConsentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config(['commenter.guest_mode.secured' => false]);
        config(['honeypot.enabled' => false]);
    }

    public function test_comment_form_renders_with_privacy_checkbox(): void
    {
        $admin = User::factory()->admin()->create();
        $post = Post::factory()->published()->create(['author_id' => $admin->id]);

        $html = Livewire::test(CommentForm::class, ['model' => $post])->html();

        $this->assertMatchesRegularExpression('/Política de\s+Privacidad y Tratamiento de Datos Personales/u', $html);
    }

    public function test_comment_fails_without_privacy_accepted(): void
    {
        $admin = User::factory()->admin()->create();
        $post = Post::factory()->published()->create(['author_id' => $admin->id]);

        Livewire::test(CommentForm::class, ['model' => $post])
            ->set('name', 'Test Guest')
            ->set('email', 'guest@example.com')
            ->set('text', 'This is a test comment.')
            ->set('privacy_accepted', false)
            ->call('create')
            ->assertHasErrors(['privacy_accepted' => 'accepted']);
    }

    public function test_comment_privacy_accepted_rule_is_present(): void
    {
        $admin = User::factory()->admin()->create();
        $post = Post::factory()->published()->create(['author_id' => $admin->id]);

        $component = Livewire::test(CommentForm::class, ['model' => $post]);

        $rules = $component->instance()->rules();

        $this->assertArrayHasKey('privacy_accepted', $rules);
        $this->assertContains('accepted', $rules['privacy_accepted']);
    }

    public function test_consent_is_persisted_after_successful_comment(): void
    {
        $admin = User::factory()->admin()->create();
        $post = Post::factory()->published()->create(['author_id' => $admin->id]);

        $this->assertDatabaseCount('comment_privacy_consents', 0);

        Livewire::test(CommentForm::class, ['model' => $post])
            ->set('name', 'Test Guest')
            ->set('email', 'guest@example.com')
            ->set('text', 'This is a test comment.')
            ->set('privacy_accepted', true)
            ->call('create')
            ->assertHasNoErrors();

        $this->assertDatabaseCount('comment_privacy_consents', 1);
        $this->assertDatabaseHas('comment_privacy_consents', [
            'commentable_type' => get_class($post),
            'commentable_id' => $post->id,
            'commenter_name' => 'Test Guest',
            'commenter_email' => 'guest@example.com',
        ]);

        $consent = CommentPrivacyConsent::first();
        $this->assertNotNull($consent->accepted_at);
        $this->assertNotNull($consent->ip);
        $this->assertNotNull($consent->user_agent);
    }

    public function test_checkbox_state_persists_after_editor_renders(): void
    {
        $admin = User::factory()->admin()->create();
        $post = Post::factory()->published()->create(['author_id' => $admin->id]);

        Livewire::test(CommentForm::class, ['model' => $post])
            ->set('text', 'First text')
            ->set('privacy_accepted', true)
            ->set('text', 'Second text after checkbox')
            ->assertSet('privacy_accepted', true);
    }
}
