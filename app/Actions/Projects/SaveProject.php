<?php

namespace App\Actions\Projects;

use App\Models\Project;
use App\Models\ProjectImage;
use Illuminate\Support\Facades\DB;

class SaveProject
{
    /**
     * @param  array<string, mixed>  $data
     * @param  array<int, array{path: string, order: int, is_cover: bool, width: ?int, height: ?int}>  $galleryImages
     */
    public function handle(
        ?Project $project,
        array $data,
        array $galleryImages,
    ): Project {
        return DB::transaction(function () use ($project, $data, $galleryImages): Project {
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

            if ($project !== null) {
                $project->update($data);
            } else {
                $project = Project::create($data);
            }

            $this->syncGallery($project, $galleryImages);

            return $project;
        });
    }

    /**
     * @param  array<int, array{path: string, order: int, is_cover: bool, width: ?int, height: ?int}>  $galleryImages
     */
    private function syncGallery(Project $project, array $galleryImages): void
    {
        $existingImages = $project->images()->get()->keyBy('image_path');
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
            $width = $imageData['width'] ?? null;
            $height = $imageData['height'] ?? null;

            if ($isCover) {
                $coverPath = $path;
            }

            if ($existingImages->has($path)) {
                $existingImages[$path]->update([
                    'order' => $index,
                    'is_cover' => $isCover,
                    ...($width !== null ? ['width' => $width] : []),
                    ...($height !== null ? ['height' => $height] : []),
                ]);
            } else {
                ProjectImage::create([
                    'project_id' => $project->id,
                    'image_path' => $path,
                    'order' => $index,
                    'is_cover' => $isCover,
                    'width' => $width,
                    'height' => $height,
                ]);
            }
        }

        if ($coverPath !== null) {
            $project->images()
                ->where('is_cover', true)
                ->where('image_path', '!=', $coverPath)
                ->update(['is_cover' => false]);
        }
    }
}
