<?php

namespace App\Livewire\Ui;

use App\Actions\Images\ConvertImageToWebp;
use App\Actions\Images\UploadImageAction;
use App\Rules\ValidCoverImage;
use Illuminate\Contracts\View\View;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Livewire\WithFileUploads;

class ImageUploader extends Component
{
    use WithFileUploads;

    public ?UploadedFile $upload = null;

    public string $path;

    public ?string $slugHint = null;

    public ?string $existingThumbUrl = null;

    public ?string $existingFullUrl = null;

    public ?string $pendingThumbPath = null;

    public ?string $pendingFullPath = null;

    public bool $loading = false;

    /** @var array{min_width:int,min_height:int,min_ratio:float,max_ratio:float}|null */
    public ?array $coverConstraints = null;

    public function mount(
        string $path,
        ?string $slugHint = null,
        ?string $existingThumbUrl = null,
        ?string $existingFullUrl = null,
        ?array $coverConstraints = null,
    ): void {
        $this->path = $path;
        $this->slugHint = $slugHint;
        $this->existingThumbUrl = $existingThumbUrl;
        $this->existingFullUrl = $existingFullUrl;
        $this->coverConstraints = $coverConstraints;
    }

    public function updatedUpload(?UploadedFile $upload): void
    {
        if ($upload === null) {
            return;
        }

        $this->upload = $upload;
        $this->uploadImage();
    }

    public function uploadImage(?UploadedFile $file = null): void
    {
        $file ??= $this->upload;

        if (! $file instanceof UploadedFile) {
            return;
        }

        $this->upload = $file;

        if ($this->isRateLimited()) {
            $this->addError('upload', __('Too many uploads. Please try again in a moment.'));

            return;
        }

        $this->hitRateLimiter();

        $this->loading = true;

        try {
            $rules = ['upload' => ['required', 'file', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048']];

            if ($this->coverConstraints !== null) {
                $c = $this->coverConstraints;
                $rules['upload'][] = new ValidCoverImage(
                    minWidth: $c['min_width'] ?? 1200,
                    minHeight: $c['min_height'] ?? 675,
                    minRatio: $c['min_ratio'] ?? 1.6,
                    maxRatio: $c['max_ratio'] ?? 2.1,
                );
            }

            $this->validate($rules);

            $paths = app(UploadImageAction::class)(
                $file,
                $this->path,
                $this->slugHint,
                'public',
            );

            $this->cleanupPending();

            $thumbUrl = Storage::disk('public')->url($paths['thumb']);
            $fullUrl = Storage::disk('public')->url($paths['full']);

            $this->existingThumbUrl = $thumbUrl;
            $this->existingFullUrl = $fullUrl;
            $this->pendingThumbPath = $paths['thumb'];
            $this->pendingFullPath = $paths['full'];
            $this->upload = null;

            $this->dispatch('image-uploaded', [
                'thumb' => $paths['thumb'],
                'full' => $paths['full'],
                'thumb_url' => $thumbUrl,
                'full_url' => $fullUrl,
            ]);
        } catch (ValidationException $e) {
            foreach ($e->errors() as $field => $messages) {
                foreach ($messages as $message) {
                    $this->addError($field, $message);
                }
            }
        } catch (\Throwable $e) {
            $this->addError('upload', $e->getMessage());
        } finally {
            $this->loading = false;
        }
    }

    public function removeImage(): void
    {
        $this->cleanupPending();
        $this->existingThumbUrl = null;
        $this->existingFullUrl = null;
        $this->upload = null;

        $this->dispatch('image-removed');
    }

    public function render(): View
    {
        return view('livewire.ui.image-uploader');
    }

    private function cleanupPending(): void
    {
        if ($this->pendingThumbPath === null && $this->pendingFullPath === null) {
            return;
        }

        app(ConvertImageToWebp::class)->delete(
            $this->pendingThumbPath ?? '',
            $this->pendingFullPath ?? '',
        );

        $this->pendingThumbPath = null;
        $this->pendingFullPath = null;
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
