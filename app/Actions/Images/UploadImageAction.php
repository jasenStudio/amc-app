<?php

namespace App\Actions\Images;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use InvalidArgumentException;
use RuntimeException;

/**
 * Centralised image-upload action.
 *
 * Validates (size, mime, dimensions), generates a semantic server-controlled
 * filename, converts to WebP via {@see ConvertImageToWebp}, and returns the
 * relative storage paths.
 */
class UploadImageAction
{
    public const MAX_BYTES = 2 * 1024 * 1024; // 2 MB

    public const MAX_WIDTH = 3000;

    public const MAX_HEIGHT = 3000;

    public const MIN_WIDTH = 1200;

    public const MIN_HEIGHT = 675;

    public const GALLERY_MIN_WIDTH = 640;

    public const GALLERY_MIN_HEIGHT = 480;

    /**
     * @var array<int, string>
     */
    public const ALLOWED_MIMES = ['image/png', 'image/jpeg', 'image/webp'];

    /**
     * Validate, name, convert and store an uploaded image as WebP.
     *
     * @param  string  $basePath  Relative directory inside the disk (e.g. `blog/webp`).
     * @param  string|null  $slugHint  Optional semantic hint derived from a title or context.
     * @param  string  $disk  Laravel filesystem disk name.
     * @param  int|null  $minWidth  Minimum width. Pass null (with $minHeight) to skip the minimum-dimensions check.
     * @param  int|null  $minHeight  Minimum height. Pass null (with $minWidth) to skip the minimum-dimensions check.
     * @param  string  $minRule  How to compare dimensions against the minimum: `both` requires both axes to meet
     *                           their respective minimum; `min` requires only `min(width, height)` to meet
     *                           `min($minWidth, $minHeight)` (orientation-agnostic).
     * @return array{thumb: string, full: string, width: int, height: int} Relative paths inside the disk.
     *
     * @throws RuntimeException When validation fails.
     * @throws InvalidArgumentException When $minRule is not `both` or `min`.
     */
    public function __invoke(
        UploadedFile $file,
        string $basePath,
        ?string $slugHint = null,
        string $disk = 'public',
        ?int $minWidth = self::MIN_WIDTH,
        ?int $minHeight = self::MIN_HEIGHT,
        string $minRule = 'both',
    ): array {
        if (! in_array($minRule, ['both', 'min'], true)) {
            throw new InvalidArgumentException(
                sprintf('Invalid minRule "%s". Expected "both" or "min".', $minRule)
            );
        }

        $this->validate($file, $minWidth, $minHeight, $minRule);

        $basename = $this->generateBasename($slugHint);

        return app(ConvertImageToWebp::class)(
            $file,
            $basePath,
            $disk,
            $basename,
        );
    }

    /**
     * @throws RuntimeException
     */
    private function validate(UploadedFile $file, ?int $minWidth, ?int $minHeight, string $minRule): void
    {
        if ($file->getSize() > self::MAX_BYTES) {
            throw new RuntimeException(
                __('Image exceeds the :max KB limit.', ['max' => self::MAX_BYTES / 1024])
            );
        }

        $mime = (string) ($file->getMimeType() ?: '');
        if (! in_array($mime, self::ALLOWED_MIMES, true)) {
            throw new RuntimeException(
                __('Image mime [:mime] is not permitted.', ['mime' => $mime])
            );
        }

        $dimensions = @getimagesize($file->getRealPath());
        if ($dimensions === false) {
            throw new RuntimeException(__('Unable to read image dimensions.'));
        }

        [$width, $height] = $dimensions;

        if ($minWidth !== null && $minHeight !== null) {
            $this->assertMinDimensions($width, $height, $minWidth, $minHeight, $minRule);
        }

        if ($width > self::MAX_WIDTH || $height > self::MAX_HEIGHT) {
            throw new RuntimeException(
                __('Image dimensions (:widthx:height) exceed the maximum (:max_widthx:max_height).', [
                    'width' => $width,
                    'height' => $height,
                    'max_width' => self::MAX_WIDTH,
                    'max_height' => self::MAX_HEIGHT,
                ])
            );
        }
    }

    /**
     * @throws RuntimeException
     */
    private function assertMinDimensions(int $width, int $height, int $minWidth, int $minHeight, string $minRule): void
    {
        $violated = $minRule === 'min'
            ? min($width, $height) < min($minWidth, $minHeight)
            : $width < $minWidth || $height < $minHeight;

        if ($violated) {
            throw new RuntimeException(
                __('Image dimensions (:widthx:height) are below the minimum (:min_widthx:min_height).', [
                    'width' => $width,
                    'height' => $height,
                    'min_width' => $minWidth,
                    'min_height' => $minHeight,
                ])
            );
        }
    }

    /**
     * Build a SEO-friendly, collision-resistant filename.
     *
     * Pattern: `<slug>-<random>.webp`
     * Falls back to `image-<random>.webp` when no semantic hint is available.
     */
    private function generateBasename(?string $slugHint): string
    {
        $slug = $slugHint !== null && $slugHint !== ''
            ? Str::slug($slugHint)
            : null;

        if ($slug === null || $slug === '') {
            $slug = 'image';
        }

        // Keep filenames reasonable; truncate long slugs.
        $slug = Str::substr($slug, 0, 80);

        return $slug.'-'.Str::random(8);
    }
}
