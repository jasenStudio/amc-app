<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;

/**
 * Resolve public URLs for stored images via the Laravel Storage abstraction.
 *
 * Using this helper instead of `asset('storage/...')` decouples the application
 * from the physical storage path, enabling a future switch to S3/R2 without
 * touching any view.
 */
final class ImageUrl
{
    /**
     * Return the public URL for a relative storage path.
     *
     * @param  string|null  $relativePath  Path relative to the disk root (e.g. `blog/webp/full/abc.webp`).
     * @param  string  $disk  Laravel filesystem disk name.
     * @return string|null Absolute URL, or null when the path is empty.
     */
    public static function public(?string $relativePath, string $disk = 'public'): ?string
    {
        if ($relativePath === null || $relativePath === '') {
            return null;
        }

        return Storage::disk($disk)->url($relativePath);
    }
}
