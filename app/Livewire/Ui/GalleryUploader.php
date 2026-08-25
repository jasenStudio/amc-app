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

    /** @var array<int, UploadedFile> */
    public array $uploads = [];

    public string $path;

    public ?string $slugHint = null;

    public bool $loading = false;

    public int $maxFiles = 20;

    /**
     * @param  array<int, array{path: string, order: int, is_cover: bool, width: ?int, height: ?int}>  $galleryImages
     */
    public function mount(
        string $path,
        ?string $slugHint = null,
        array $galleryImages = [],
        int $maxFiles = 20,
    ): void {
        $this->path = $path;
        $this->slugHint = $slugHint;
        $this->maxFiles = $maxFiles;
    }

    public function updatedUploads(): void
    {
        if (empty($this->uploads)) {
            return;
        }

        if ($this->isRateLimited()) {
            $this->addError('uploads', __('Too many uploads. Please try again in a moment.'));
            $this->uploads = [];

            return;
        }

        $this->hitRateLimiter();
        $this->loading = true;

        try {
            foreach ($this->uploads as $upload) {
                if (! $upload instanceof UploadedFile) {
                    continue;
                }

                $rules = ['uploads.*' => ['file', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048']];
                $this->validate($rules);

                $paths = app(UploadImageAction::class)(
                    $upload,
                    $this->path,
                    $this->slugHint,
                    'public',
                );

                $this->dispatch('gallery-image-uploaded', [
                    'path' => $paths['full'],
                    'url' => Storage::disk('public')->url($paths['full']),
                    'width' => $paths['width'],
                    'height' => $paths['height'],
                ]);
            }

            $this->uploads = [];
        } catch (\Throwable $e) {
            $this->addError('uploads', $e->getMessage());
        } finally {
            $this->loading = false;
        }
    }

    public function render(): View
    {
        return view('livewire.ui.gallery-uploader-simple');
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
