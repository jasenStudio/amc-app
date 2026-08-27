<?php

namespace App\Actions\Contact;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class VerifyRecaptcha
{
    public function handle(string $token, string $expectedAction): bool
    {
        $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret' => config('services.recaptcha.secret_key'),
            'response' => $token,
        ]);

        if ($response->failed()) {
            Log::warning('reCAPTCHA verification request failed', [
                'status' => $response->status(),
            ]);

            return false;
        }

        $data = $response->json();

        if (empty($data['success'])) {
            return false;
        }

        if (($data['action'] ?? '') !== $expectedAction) {
            return false;
        }

        if (($data['score'] ?? 0) < config('services.recaptcha.min_score', 0.5)) {
            return false;
        }

        return true;
    }
}
