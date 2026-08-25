<?php

namespace App\Console\Commands;

use App\Models\ProjectImage;
use App\Models\ServiceImage;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Throwable;

#[Signature('images:backfill-dimensions')]
#[Description('Read missing width/height dimensions for project and service gallery images.')]
class BackfillImageDimensions extends Command
{
    public function handle(): int
    {
        $projectCount = $this->backfill(ProjectImage::class);
        $serviceCount = $this->backfill(ServiceImage::class);

        $this->info(sprintf(
            'Backfilled dimensions for %d project image(s) and %d service image(s).',
            $projectCount,
            $serviceCount,
        ));

        return self::SUCCESS;
    }

    /**
     * @param  class-string<ProjectImage|ServiceImage>  $model
     */
    private function backfill(string $model): int
    {
        $updated = 0;

        $model::query()
            ->whereNull('width')
            ->orWhereNull('height')
            ->chunk(50, function ($images) use (&$updated): void {
                foreach ($images as $image) {
                    $dimensions = $this->readDimensions($image->image_path);

                    if ($dimensions === null) {
                        continue;
                    }

                    $image->update([
                        'width' => $dimensions[0],
                        'height' => $dimensions[1],
                    ]);

                    $updated++;
                }
            });

        return $updated;
    }

    /**
     * @return array{0: int, 1: int}|null
     */
    private function readDimensions(string $path): ?array
    {
        try {
            $contents = Storage::disk('public')->get($path);
        } catch (Throwable) {
            return null;
        }

        if ($contents === false || $contents === null || $contents === '') {
            return null;
        }

        $size = @getimagesizefromstring($contents);

        if ($size === false) {
            return null;
        }

        return [(int) $size[0], (int) $size[1]];
    }
}
