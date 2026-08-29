<?php

namespace App\Livewire\Ui;

use App\Actions\Images\UploadImageAction;
use Illuminate\Contracts\View\View;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Livewire\WithFileUploads;

class GalleryUploader extends Component
{
    use WithFileUploads;

    /** @var array<int, UploadedFile> */
    public array $uploads = [];

    public string $path;

    public ?string $slugHint = null;

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

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'uploads.*.required' => __('Each image is required.'),
            'uploads.*.image' => __('Each file must be a valid image.'),
            'uploads.*.mimes' => __('Each image must be a JPG, PNG or WebP file.'),
            'uploads.*.max' => __('Each image must not be larger than :max MB.', ['max' => 2]),
        ];
    }

    /**
     * @return array<string, string>
     */
    public function validationAttributes(): array
    {
        return [
            'uploads.*' => __('image'),
        ];
    }

    public function updatedUploads(): void
    {
        if (empty($this->uploads)) {
            return;
        }

        // Limpia errores de un intento anterior antes de procesar el nuevo lote.
        $this->resetErrorBag('uploads');

        if ($this->isRateLimited()) {
            $this->addError('uploads', __('Too many uploads. Please try again in a moment.'));
            $this->uploads = [];

            return;
        }

        $this->hitRateLimiter();

        try {
            $this->validate([
                'uploads.*' => ['file', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            ]);
        } catch (ValidationException $e) {
            $indexes = collect($this->uploads)->keys();
            foreach ($e->errors() as $field => $messages) {
                $index = (int) Str::afterLast($field, '.');
                $filename = $indexes->has($index)
                    ? $this->uploads[$index]->getClientOriginalName()
                    : null;

                foreach ($messages as $message) {
                    $this->addError('uploads', $filename !== null ? $filename.': '.$message : $message);
                }
            }
            $this->uploads = [];

            return;
        }

        foreach ($this->uploads as $upload) {
            try {
                $paths = app(UploadImageAction::class)(
                    $upload,
                    $this->path,
                    $this->slugHint,
                    'public',
                    minWidth: UploadImageAction::GALLERY_MIN_WIDTH,
                    minHeight: UploadImageAction::GALLERY_MIN_HEIGHT,
                    minRule: 'min',
                );

                $this->dispatch('gallery-image-uploaded', [
                    'path' => $paths['full'],
                    'url' => Storage::disk('public')->url($paths['full']),
                    'width' => $paths['width'],
                    'height' => $paths['height'],
                ]);
            } catch (\Throwable $e) {
                // Un archivo individual falla (p.ej. dimensiones fuera de rango)
                // sin descartar el resto del lote.
                $this->addError('uploads', $upload->getClientOriginalName().': '.$e->getMessage());
            }
        }

        $this->uploads = [];
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
