<?php

namespace App\Console\Commands;

use App\Models\Image;
use App\Models\Post;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

#[Signature('blog:cleanup-orphan-images {--dry-run : List files that would be deleted without deleting them}')]
#[Description('Delete image files under blog/webp that are no longer referenced by any image record, post SEO image or post body.')]
class CleanupOrphanImages extends Command
{
    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $referenced = $this->collectReferencedPaths();
        $files = Storage::disk('public')->allFiles('blog/webp');

        $removed = 0;

        foreach ($files as $file) {
            if (in_array($file, $referenced, true)) {
                continue;
            }

            $removed++;

            if ($dryRun) {
                $this->line("Would delete: {$file}");
            } else {
                Storage::disk('public')->delete($file);
                $this->line("Deleted: {$file}");
            }
        }

        $this->info(sprintf(
            '%d orphan file(s) %s.',
            $removed,
            $dryRun ? 'would be deleted' : 'deleted',
        ));

        return self::SUCCESS;
    }

    /**
     * @return array<int, string> Normalized relative storage paths that are still in use.
     */
    private function collectReferencedPaths(): array
    {
        $paths = [];

        foreach (Image::query()->pluck('thumb_path')->concat(Image::query()->pluck('full_path')) as $path) {
            if (is_string($path) && $path !== '') {
                $paths[] = $this->normalizeReferencedPath($path);
            }
        }

        foreach (Post::withTrashed()->pluck('seo_image') as $path) {
            if (is_string($path) && $path !== '') {
                $paths[] = $this->normalizeReferencedPath($path);
            }
        }

        foreach (Post::withTrashed()->pluck('body') as $body) {
            foreach ($this->extractBodyImagePaths((string) $body) as $path) {
                $paths[] = $this->normalizeReferencedPath($path);
            }
        }

        return array_values(array_unique($paths));
    }

    /**
     * @return array<int, string>
     */
    private function extractBodyImagePaths(string $body): array
    {
        $paths = [];

        if (! preg_match_all('/src\s*=\s*["\']([^"\']+)["\']/', $body, $matches)) {
            return $paths;
        }

        foreach ($matches[1] as $url) {
            if (str_contains($url, 'storage/') || str_contains($url, 'blog/webp')) {
                $paths[] = $url;
            }
        }

        return $paths;
    }

    private function normalizeReferencedPath(string $path): string
    {
        $path = preg_replace('#^https?://[^/]+/?#', '', $path) ?? $path;
        $path = ltrim($path, '/');

        if (str_starts_with($path, 'storage/')) {
            $path = substr($path, strlen('storage/'));
        }

        $path = preg_replace('/[?#].*$/', '', $path) ?? $path;

        return trim($path, '/');
    }
}
