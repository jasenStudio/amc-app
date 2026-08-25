<?php

namespace App\Rules;

use App\Support\VideoEmbed;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidVideoUrl implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($value === null || $value === '') {
            return;
        }

        if (VideoEmbed::provider($value) === null) {
            $fail(__('The video URL must be a valid YouTube or Vimeo link.'));
        }
    }
}
