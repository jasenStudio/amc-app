<?php

namespace App\Actions\Images;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/**
 * Upload an inline image from the Tiptap editor.
 *
 * Delegates validation, naming and conversion to {@see UploadImageAction}
 * and returns a public URL compatible with the Tiptap `setImage` command.
 */
class UploadInlineImage
{
    public function __invoke(UploadedFile $file, string $disk = 'public'): string
    {
        $paths = app(UploadImageAction::class)(
            $file,
            'blog/webp/body',
            'inline',
            $disk,
        );

        return Storage::disk($disk)->url($paths['full']);
    }
}
