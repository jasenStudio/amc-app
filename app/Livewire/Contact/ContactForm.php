<?php

namespace App\Livewire\Contact;

use App\Actions\Contact\SaveContactMessage;
use App\Actions\Contact\VerifyRecaptcha;
use App\Models\Service;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Component;

class ContactForm extends Component
{
    public string $name = '';

    public ?string $company = null;

    public ?int $service_id = null;

    public string $message = '';

    public bool $privacy_accepted = false;

    public bool $submitted = false;

    public bool $submitting = false;

    /**
     * @return array<string, array<int, string>>
     */
    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'company' => ['nullable', 'string', 'max:255'],
            'service_id' => ['nullable', 'exists:services,id'],
            'message' => ['required', 'string', 'min:10'],
            'privacy_accepted' => ['accepted'],
        ];
    }

    public function submit(string $recaptchaToken, SaveContactMessage $action): void
    {
        $this->submitting = true;

        $verifyRecaptcha = app(VerifyRecaptcha::class);

        if (! $verifyRecaptcha->handle($recaptchaToken, 'contact')) {
            $this->addError('message', __('reCAPTCHA verification failed. Please try again.'));
            $this->submitting = false;

            return;
        }

        $this->validate();

        if (RateLimiter::tooManyAttempts('contact|'.request()->ip(), 5)) {
            $this->addError('message', __('Too many attempts. Please try again later.'));
            $this->submitting = false;

            return;
        }

        RateLimiter::hit('contact|'.request()->ip(), 60);

        $action->handle([
            'name' => $this->name,
            'company' => $this->company,
            'service_id' => $this->service_id,
            'message' => $this->message,
            'privacy_accepted_at' => $this->privacy_accepted ? now() : null,
        ]);

        $this->submitted = true;
        $this->submitting = false;
        $this->reset(['name', 'company', 'service_id', 'message', 'privacy_accepted']);
    }

    public function render(): View
    {
        $services = Service::active()->ordered()->get();

        return view('livewire.contact.contact-form', compact('services'));
    }
}
