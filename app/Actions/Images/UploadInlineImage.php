<?php

namespace App\Actions\Images;

use Illuminate\Http\UploadedFile;
use RuntimeException;

class UploadInlineImage
{
    public const MAX_BYTES = 4 * 1024 * 1024;

    /**
     * @var array<int, string>
     */
    public const ALLOWED_MIMES = ['image/png', 'image/jpeg', 'image/webp'];

    /**
     * Convert an inline editor upload to webp and return its public URL.
     */
    public function __invoke(UploadedFile $file, string $disk = 'public'): string
    {
        if ($file->getSize() > self::MAX_BYTES) {
            throw new RuntimeException('Inline image exceeds the 4MB limit.');
        }

        $mime = (string) ($file->getMimeType() ?: '');
        if (! in_array($mime, self::ALLOWED_MIMES, true)) {
            throw new RuntimeException("Inline image mime [{$mime}] is not permitted.");
        }

        $paths = app(ConvertImageToWebp::class)($file, 'blog/webp/body', $disk);

        return asset('storage/'.$paths['full']);
    }
}
