<?php

namespace App\Livewire\Ui;

use Illuminate\Contracts\View\View;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class ImageUploader extends Component
{
    use WithFileUploads;

    public ?UploadedFile $upload = null;

    public string $endpoint;

    public string $path;

    public ?string $slugHint = null;

    public ?string $existingThumbUrl = null;

    public ?string $existingFullUrl = null;

    public bool $loading = false;

    public function mount(
        string $endpoint,
        string $path,
        ?string $slugHint = null,
        ?string $existingThumbUrl = null,
        ?string $existingFullUrl = null,
    ): void {
        $this->endpoint = $endpoint;
        $this->path = $path;
        $this->slugHint = $slugHint;
        $this->existingThumbUrl = $existingThumbUrl;
        $this->existingFullUrl = $existingFullUrl;
    }

    public function uploadImage(): void
    {
        if (!$this->upload instanceof UploadedFile) {
            return;
        }

        $this->loading = true;

        try {
            $response = $this->uploadToEndpoint($this->upload);

            if ($response['success']) {
                $this->existingThumbUrl = $response['thumb_url'];
                $this->existingFullUrl = $response['full_url'];
                $this->upload = null;

                $this->dispatch('image-uploaded', [
                    'thumb' => $response['thumb'],
                    'full' => $response['full'],
                    'url' => $response['full_url'],
                ]);
            } else {
                $this->addError('upload', $response['error']);
            }
        } finally {
            $this->loading = false;
        }
    }

    public function removeImage(): void
    {
        $this->existingThumbUrl = null;
        $this->existingFullUrl = null;
        $this->upload = null;

        $this->dispatch('image-removed');
    }

    public function render(): View
    {
        return view('livewire.ui.image-uploader');
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
