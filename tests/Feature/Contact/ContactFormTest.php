<?php

namespace Tests\Feature\Contact;

use App\Livewire\Contact\ContactForm;
use App\Mail\ContactMessageMail;
use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use Tests\TestCase;

class ContactFormTest extends TestCase
{
    use RefreshDatabase;

    protected function fakeRecaptchaSuccess(): void
    {
        Http::fake([
            'www.google.com/recaptcha/api/siteverify' => Http::response([
                'success' => true,
                'action' => 'contact',
                'score' => 0.9,
            ]),
        ]);
    }

    public function test_renders_successfully(): void
    {
        Livewire::test(ContactForm::class)
            ->assertStatus(200)
            ->assertSee('Nombre completo')
            ->assertSee('Mensaje');
    }

    public function test_validates_required_fields(): void
    {
        $this->fakeRecaptchaSuccess();

        Livewire::test(ContactForm::class)
            ->call('submit', 'test-token')
            ->assertHasErrors(['name' => 'required', 'message' => 'required']);
    }

    public function test_validates_message_min_length(): void
    {
        $this->fakeRecaptchaSuccess();

        Livewire::test(ContactForm::class)
            ->set('name', 'John Doe')
            ->set('message', 'short')
            ->call('submit', 'test-token')
            ->assertHasErrors(['message' => 'min']);
    }

    public function test_creates_contact_message_and_sends_email(): void
    {
        $this->fakeRecaptchaSuccess();
        Mail::fake();

        $service = Service::factory()->create();

        Livewire::test(ContactForm::class)
            ->set('name', 'John Doe')
            ->set('company', 'ACME Corp')
            ->set('service_id', $service->id)
            ->set('message', 'This is a test message with enough length.')
            ->call('submit', 'test-token')
            ->assertHasNoErrors()
            ->assertSet('submitted', true);

        $this->assertDatabaseHas('contact_messages', [
            'name' => 'John Doe',
            'company' => 'ACME Corp',
            'service_id' => $service->id,
            'message' => 'This is a test message with enough length.',
        ]);

        Mail::assertSent(ContactMessageMail::class, function ($mail) {
            return $mail->hasTo('gerencia@amcgestiondelriesgo.com.co');
        });
    }

    public function test_resets_form_after_submission(): void
    {
        $this->fakeRecaptchaSuccess();
        Mail::fake();

        Livewire::test(ContactForm::class)
            ->set('name', 'John Doe')
            ->set('message', 'This is a test message with enough length.')
            ->call('submit', 'test-token')
            ->assertSet('name', '')
            ->assertSet('message', '');
    }

    public function test_validates_service_id_exists(): void
    {
        $this->fakeRecaptchaSuccess();

        Livewire::test(ContactForm::class)
            ->set('name', 'John Doe')
            ->set('service_id', 999)
            ->set('message', 'This is a test message with enough length.')
            ->call('submit', 'test-token')
            ->assertHasErrors(['service_id' => 'exists']);
    }

    public function test_fails_when_recaptcha_score_is_too_low(): void
    {
        Http::fake([
            'www.google.com/recaptcha/api/siteverify' => Http::response([
                'success' => true,
                'action' => 'contact',
                'score' => 0.1,
            ]),
        ]);

        Livewire::test(ContactForm::class)
            ->set('name', 'John Doe')
            ->set('message', 'This is a test message with enough length.')
            ->call('submit', 'test-token')
            ->assertHasErrors(['message'])
            ->assertSet('submitted', false)
            ->assertSet('submitting', false);
    }

    public function test_fails_when_recaptcha_action_does_not_match(): void
    {
        Http::fake([
            'www.google.com/recaptcha/api/siteverify' => Http::response([
                'success' => true,
                'action' => 'login',
                'score' => 0.9,
            ]),
        ]);

        Livewire::test(ContactForm::class)
            ->set('name', 'John Doe')
            ->set('message', 'This is a test message with enough length.')
            ->call('submit', 'test-token')
            ->assertHasErrors(['message'])
            ->assertSet('submitted', false);
    }

    public function test_fails_when_recaptcha_request_fails(): void
    {
        Http::fake([
            'www.google.com/recaptcha/api/siteverify' => Http::response('Server Error', 500),
        ]);

        Livewire::test(ContactForm::class)
            ->set('name', 'John Doe')
            ->set('message', 'This is a test message with enough length.')
            ->call('submit', 'test-token')
            ->assertHasErrors(['message'])
            ->assertSet('submitted', false);
    }

    public function test_submitting_is_reset_after_successful_submission(): void
    {
        $this->fakeRecaptchaSuccess();
        Mail::fake();

        Livewire::test(ContactForm::class)
            ->set('name', 'John Doe')
            ->set('message', 'This is a test message with enough length.')
            ->call('submit', 'test-token')
            ->assertSet('submitting', false)
            ->assertSet('submitted', true);
    }
}
