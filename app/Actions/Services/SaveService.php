<?php

namespace App\Actions\Services;

use App\Models\Service;
use App\Models\ServiceImage;
use Illuminate\Support\Facades\DB;

class SaveService
{
    /**
     * @param  array<string, mixed>  $data
     * @param  array<int, array{path: string, order: int, is_cover: bool}>  $galleryImages
     */
    public function handle(
        ?Service $service,
        array $data,
        array $galleryImages,
    ): Service {
        return DB::transaction(function () use ($service, $data, $galleryImages): Service {
            $hasCover = false;
            foreach ($galleryImages as $img) {
                if (! empty($img['is_cover'])) {
                    $hasCover = true;
                    break;
                }
            }

            if (! $hasCover && count($galleryImages) > 0) {
                $galleryImages[0]['is_cover'] = true;
            }

            if ($service !== null) {
                $service->update($data);
            } else {
                $service = Service::create($data);
            }

            $this->syncGallery($service, $galleryImages);

            return $service;
        });
    }

    /**
     * @param  array<int, array{path: string, order: int, is_cover: bool}>  $galleryImages
     */
    private function syncGallery(Service $service, array $galleryImages): void
    {
        $existingImages = $service->images()->get()->keyBy('image_path');
        $newPaths = collect($galleryImages)->pluck('path')->all();

        foreach ($existingImages as $path => $image) {
            if (! in_array($path, $newPaths, true)) {
                $image->delete();
            }
        }

        $coverPath = null;

        foreach ($galleryImages as $index => $imageData) {
            $path = $imageData['path'];
            $isCover = (bool) ($imageData['is_cover'] ?? false);

            if ($isCover) {
                $coverPath = $path;
            }

            if ($existingImages->has($path)) {
                $existingImages[$path]->update([
                    'order' => $index,
                    'is_cover' => $isCover,
                ]);
            } else {
                ServiceImage::create([
                    'service_id' => $service->id,
                    'image_path' => $path,
                    'order' => $index,
                    'is_cover' => $isCover,
                ]);
            }
        }

        if ($coverPath !== null) {
            $service->images()
                ->where('is_cover', true)
                ->where('image_path', '!=', $coverPath)
                ->update(['is_cover' => false]);
        }
    }
}
