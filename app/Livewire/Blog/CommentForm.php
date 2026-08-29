<?php

namespace App\Livewire\Blog;

use App\Models\CommentPrivacyConsent;
use Illuminate\Contracts\View\View;
use LakM\Commenter\Livewire\Comments\CreateForm;
use LakM\Commenter\ValidationRules;

class CommentForm extends CreateForm
{
    public bool $privacy_accepted = false;

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return array_merge(
            ValidationRules::get($this->model, 'create'),
            ['privacy_accepted' => ['accepted']]
        );
    }

    public function create(): void
    {
        parent::create();

        if (! $this->getErrorBag()->isEmpty()) {
            return;
        }

        CommentPrivacyConsent::create([
            'commentable_type' => get_class($this->model),
            'commentable_id' => $this->model->getKey(),
            'commenter_name' => $this->name ?: null,
            'commenter_email' => $this->email ?: null,
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'accepted_at' => now(),
        ]);
    }

    public function clear(): void
    {
        parent::clear();

        $this->reset('privacy_accepted');
    }

    public function render(): View
    {
        return view('livewire.blog.comment-form');
    }
}
