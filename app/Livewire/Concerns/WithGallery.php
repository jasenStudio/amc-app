<?php

namespace App\Livewire\Concerns;

use Livewire\Attributes\On;

/**
 * @property array<int, array{path: string, order: int, is_cover: bool}> $galleryImages
 */
trait WithGallery
{
    /** @var array<int, array{path: string, order: int, is_cover: bool}> */
    public array $galleryImages = [];

    /**
     * @param  array{path?: string, order?: int, is_cover?: bool}  $imageData
     */
    #[On('gallery-image-uploaded')]
    public function onGalleryImageUploaded(array $imageData): void
    {
        $path = $imageData['path'] ?? null;
        if ($path === null || $path === '') {
            return;
        }

        $isCover = (bool) ($imageData['is_cover'] ?? false);

        if ($isCover) {
            foreach ($this->galleryImages as $idx => $img) {
                $this->galleryImages[$idx]['is_cover'] = false;
            }
        }

        $this->galleryImages[] = [
            'path' => $path,
            'order' => count($this->galleryImages),
            'is_cover' => $isCover && count($this->galleryImages) === 0,
        ];
    }

    public function removeGalleryImage(int $index): void
    {
        if (! isset($this->galleryImages[$index])) {
            return;
        }

        $wasCover = $this->galleryImages[$index]['is_cover'] ?? false;

        array_splice($this->galleryImages, $index, 1);

        foreach ($this->galleryImages as $idx => $img) {
            $this->galleryImages[$idx]['order'] = $idx;
        }

        if ($wasCover && count($this->galleryImages) > 0) {
            $this->galleryImages[0]['is_cover'] = true;
        }
    }

    public function setCoverImage(int $index): void
    {
        if (! isset($this->galleryImages[$index])) {
            return;
        }

        foreach ($this->galleryImages as $idx => $img) {
            $this->galleryImages[$idx]['is_cover'] = $idx === $index;
        }
    }

    public function moveGalleryImageUp(int $index): void
    {
        if ($index <= 0 || ! isset($this->galleryImages[$index])) {
            return;
        }

        $temp = $this->galleryImages[$index];
        $this->galleryImages[$index] = $this->galleryImages[$index - 1];
        $this->galleryImages[$index - 1] = $temp;

        foreach ($this->galleryImages as $idx => $img) {
            $this->galleryImages[$idx]['order'] = $idx;
        }
    }

    public function moveGalleryImageDown(int $index): void
    {
        if (! isset($this->galleryImages[$index]) || ! isset($this->galleryImages[$index + 1])) {
            return;
        }

        $temp = $this->galleryImages[$index];
        $this->galleryImages[$index] = $this->galleryImages[$index + 1];
        $this->galleryImages[$index + 1] = $temp;

        foreach ($this->galleryImages as $idx => $img) {
            $this->galleryImages[$idx]['order'] = $idx;
        }
    }
}
