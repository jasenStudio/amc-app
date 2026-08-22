<?php

namespace App\Livewire\Concerns;

use Livewire\Attributes\On;

trait WithCoverImage
{
    public ?string $coverImageThumb = null;

    public ?string $coverImageFull = null;

    public ?string $coverImageThumbPath = null;

    public ?string $coverImageFullPath = null;

    public bool $shouldRemoveCover = false;

    /**
     * @param  array{thumb_url?: string, full_url?: string, thumb?: string, full?: string}  $imageData
     */
    #[On('image-uploaded')]
    public function onImageUploaded(array $imageData): void
    {
        $this->coverImageThumb = $imageData['thumb_url'] ?? null;
        $this->coverImageFull = $imageData['full_url'] ?? null;
        $this->coverImageThumbPath = $imageData['thumb'] ?? null;
        $this->coverImageFullPath = $imageData['full'] ?? null;
        $this->shouldRemoveCover = false;
    }

    #[On('image-removed')]
    public function onImageRemoved(): void
    {
        $this->coverImageThumb = null;
        $this->coverImageFull = null;
        $this->coverImageThumbPath = null;
        $this->coverImageFullPath = null;
        $this->shouldRemoveCover = true;
    }
}
