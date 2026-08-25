<?php

namespace App\Actions\Images;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;
use Intervention\Image\Drivers\Imagick\Driver as ImagickDriver;
use Intervention\Image\Encoders\WebpEncoder;
use Intervention\Image\ImageManager;
use Intervention\Image\Interfaces\ImageInterface;
use RuntimeException;

class ConvertImageToWebp
{
    private const THUMB_WIDTH = 640;

    private const FULL_WIDTH = 1600;

    private const QUALITY = 82;

    /**
     * Convert an uploaded image to webp, generate a thumbnail and a
     * full-size variant, and store both on the given disk under
     * `<basePath>/<thumbs|full>/<basename>.webp`.
     *
     * @param  string  $basePath  Relative directory inside the disk.
     * @param  string  $disk  Laravel filesystem disk name.
     * @param  string|null  $basename  Pre-generated basename. When null a ULID is used (legacy).
     * @return array{thumb: string, full: string, width: int, height: int}
     *                                                                     Relative paths inside the disk (e.g. "blog/webp/thumbs/abc.webp").
     */
    public function __invoke(
        UploadedFile $file,
        string $basePath,
        string $disk = 'public',
        ?string $basename = null,
    ): array {
        $manager = $this->makeManager();

        $image = $manager->decode($file->getRealPath());

        $basename ??= $this->uniqueBasename($file);

        $fullImage = $image->scale(width: self::FULL_WIDTH);
        $width = $fullImage->width();
        $height = $fullImage->height();
        $full = $this->store($fullImage, $disk, $basePath, 'full', $basename);

        $thumb = $this->store($image->scale(width: self::THUMB_WIDTH), $disk, $basePath, 'thumbs', $basename);

        return [
            'thumb' => $thumb,
            'full' => $full,
            'width' => $width,
            'height' => $height,
        ];
    }

    /**
     * Delete the asset pair produced by `__invoke`. Returns true when
     * at least one file was removed.
     */
    public function delete(string $relativeThumbPath, string $relativeFullPath, string $disk = 'public'): bool
    {
        $fs = Storage::disk($disk);

        $removed = false;
        foreach ([$relativeThumbPath, $relativeFullPath] as $path) {
            if ($path !== '' && $fs->exists($path)) {
                $fs->delete($path);
                $removed = true;
            }
        }

        return $removed;
    }

    /**
     * Delete a single image file. Returns true when the file was removed.
     */
    public function deleteSingle(string $relativePath, string $disk = 'public'): bool
    {
        $fs = Storage::disk($disk);

        if ($relativePath !== '' && $fs->exists($relativePath)) {
            return $fs->delete($relativePath);
        }

        return false;
    }

    private function store(
        ImageInterface $image,
        string $disk,
        string $basePath,
        string $subdir,
        string $basename,
    ): string {
        $path = trim($basePath.'/'.$subdir.'/'.$basename.'.webp', '/');

        Storage::disk($disk)->put(
            $path,
            (string) $image->encode(new WebpEncoder(quality: self::QUALITY))
        );

        return $path;
    }

    private function uniqueBasename(UploadedFile $file): string
    {
        return Str::ulid();
    }

    private function makeManager(): ImageManager
    {
        if (extension_loaded('imagick')) {
            return new ImageManager(new ImagickDriver);
        }

        if (extension_loaded('gd')) {
            return new ImageManager(new GdDriver);
        }

        throw new RuntimeException('No image driver available. Install the GD or Imagick PHP extension.');
    }
}
