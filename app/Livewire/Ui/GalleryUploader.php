<?php

namespace App\Livewire\Ui;

use App\Actions\Images\UploadImageAction;
use Illuminate\Contracts\View\View;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class GalleryUploader extends Component
{
    use WithFileUploads;

    /** @var UploadedFile[] */
    public array $uploads = [];

    public string $path;

    public ?string $slugHint = null;

    public int $maxFiles = 10;

    /** @var array<int, array{thumb: string, full: string, url: string, alt?: string}> */
    public array $images = [];

    public bool $loading = false;

    /**
     * @param  array<int, array{thumb: string, full: string, url: string, alt?: string}>  $images
     */
    public function mount(
        string $path,
        ?string $slugHint = null,
        int $maxFiles = 10,
        array $images = [],
    ): void {
        $this->path = $path;
        $this->slugHint = $slugHint;
        $this->maxFiles = $maxFiles;
        $this->images = $images;
    }

    /**
     * @param  array<int, UploadedFile>  $uploads
     */
    public function updatedUploads(array $uploads): void
    {
        if (empty($uploads)) {
            return;
        }

        $this->uploadImages();
    }

    public function uploadImages(): void
    {
        if (empty($this->uploads)) {
            return;
        }

        $remaining = $this->maxFiles - count($this->images);
        if ($remaining <= 0) {
            $this->addError('uploads', __('Maximum number of images reached.'));
            $this->uploads = [];

            return;
        }

        if ($this->isRateLimited()) {
            $this->addError('uploads', __('Too many uploads. Please try again in a moment.'));

            return;
        }

        $this->loading = true;

        try {
            $toUpload = array_slice($this->uploads, 0, $remaining);

            foreach ($toUpload as $upload) {
                $this->hitRateLimiter();

                $paths = app(UploadImageAction::class)(
                    $upload,
                    $this->path,
                    $this->slugHint,
                    'public',
                );

                $this->images[] = [
                    'thumb' => $paths['thumb'],
                    'full' => $paths['full'],
                    'url' => Storage::disk('public')->url($paths['full']),
                    'alt' => '',
                ];
            }

            $this->uploads = [];

            $this->dispatch('gallery-updated', $this->images);
        } catch (\Throwable $e) {
            $this->addError('uploads', $e->getMessage());
        } finally {
            $this->loading = false;
        }
    }

    public function removeImage(int $index): void
    {
        if (isset($this->images[$index])) {
            array_splice($this->images, $index, 1);
            $this->dispatch('gallery-updated', $this->images);
        }
    }

    public function render(): View
    {
        return view('livewire.ui.gallery-uploader');
    }

    private function isRateLimited(): bool
    {
        return RateLimiter::tooManyAttempts($this->rateLimiterKey(), 15);
    }

    private function hitRateLimiter(): void
    {
        RateLimiter::hit($this->rateLimiterKey(), 60);
    }

    private function rateLimiterKey(): string
    {
        return 'dashboard-images|'.(auth()->id() ?: request()->ip());
    }
}
