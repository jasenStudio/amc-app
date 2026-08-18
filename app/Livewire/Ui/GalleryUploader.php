<?php

namespace App\Livewire\Ui;

use Illuminate\Contracts\View\View;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class GalleryUploader extends Component
{
    use WithFileUploads;

    /** @var UploadedFile[] */
    public array $uploads = [];

    public string $endpoint;

    public string $path;

    public ?string $slugHint = null;

    public int $maxFiles = 10;

    /** @var array<int, array{thumb: string, full: string, url: string, alt?: string}> */
    public array $images = [];

    public bool $loading = false;

    public function mount(
        string $endpoint,
        string $path,
        ?string $slugHint = null,
        int $maxFiles = 10,
        array $images = [],
    ): void {
        $this->endpoint = $endpoint;
        $this->path = $path;
        $this->slugHint = $slugHint;
        $this->maxFiles = $maxFiles;
        $this->images = $images;
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

        $this->loading = true;

        try {
            $toUpload = array_slice($this->uploads, 0, $remaining);

            foreach ($toUpload as $upload) {
                $response = $this->uploadToEndpoint($upload);

                if ($response['success']) {
                    $this->images[] = [
                        'thumb' => $response['thumb'],
                        'full' => $response['full'],
                        'url' => $response['full_url'],
                        'alt' => '',
                    ];
                } else {
                    $this->addError('uploads', $response['error']);
                    break;
                }
            }

            $this->uploads = [];

            $this->dispatch('gallery-updated', $this->images);
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

    private function uploadToEndpoint(UploadedFile $file): array
    {
        $csrf = csrf_token();

        $ch = curl_init($this->endpoint);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => [
                'upload' => curl_file_create(
                    $file->getRealPath(),
                    $file->getMimeType(),
                    $file->getClientOriginalName(),
                ),
                'path' => $this->path,
                'slugHint' => $this->slugHint ?? '',
            ],
            CURLOPT_HTTPHEADER => [
                'X-CSRF-TOKEN: ' . $csrf,
                'Accept: application/json',
            ],
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200 || !$response) {
            return [
                'success' => false,
                'error' => 'Upload failed. Please try again.',
            ];
        }

        $data = json_decode($response, true);

        if (!isset($data['thumb'], $data['full'], $data['url'])) {
            return [
                'success' => false,
                'error' => 'Invalid response from server.',
            ];
        }

        return [
            'success' => true,
            'thumb' => $data['thumb'],
            'full' => $data['full'],
            'thumb_url' => Storage::disk('public')->url($data['thumb']),
            'full_url' => $data['url'],
        ];
    }
}
