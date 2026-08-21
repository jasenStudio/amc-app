<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Http\UploadedFile;

class ValidCoverImage implements ValidationRule
{
    public function __construct(
        private readonly int $minWidth = 1200,
        private readonly int $minHeight = 675,
        private readonly float $minRatio = 1.6,
        private readonly float $maxRatio = 2.1,
    ) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! $value instanceof UploadedFile || ! $value->isValid()) {
            $fail(__('Cover image must be a valid file.'));

            return;
        }

        $dims = @getimagesize($value->getRealPath());

        if ($dims === false) {
            $fail(__('Could not read cover image dimensions.'));

            return;
        }

        [$w, $h] = $dims;

        if ($w < $this->minWidth) {
            $fail(__('Cover image width must be at least :width pixels.', ['width' => $this->minWidth]));

            return;
        }

        if ($h < $this->minHeight) {
            $fail(__('Cover image height must be at least :height pixels.', ['height' => $this->minHeight]));

            return;
        }

        $ratio = $w / $h;

        if ($ratio < $this->minRatio || $ratio > $this->maxRatio) {
            $fail(__('Cover image aspect ratio must be between :min and :max (close to 16:9). Got :actual.', [
                'min' => $this->minRatio,
                'max' => $this->maxRatio,
                'actual' => round($ratio, 2),
            ]));
        }
    }
}
