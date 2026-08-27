<?php

namespace App\Actions\Contact;

use App\Mail\ContactMessageMail;
use App\Models\ContactMessage;
use Illuminate\Support\Facades\Mail;

class SaveContactMessage
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function handle(array $data): ContactMessage
    {
        $message = ContactMessage::create([
            'name' => $data['name'],
            'company' => $data['company'] ?? null,
            'service_id' => $data['service_id'] ?? null,
            'message' => $data['message'],
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        Mail::to('gerencia@amcgestiondelriesgo.com.co')->send(new ContactMessageMail($message));

        return $message;
    }
}
