<?php

namespace App\Livewire\Services;

use App\Actions\Services\SaveService;
use App\Livewire\Concerns\WithGallery;
use App\Models\Service;
use App\Support\SlugGenerator;
use Flux\Flux as FluxFacade;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.dashboard')]
class ServiceForm extends Component
{
    use WithGallery;

    public ?Service $service = null;

    public string $title = '';

    public string $slug = '';

    public string $description = '';

    public string $excerpt = '';

    public ?string $price = null;

    public string $status = 'active';

    public bool $featured = false;

    public int $order = 0;

    public string $title_seo = '';

    public function mount(int $serviceId = 0): void
    {
        if ($serviceId === 0) {
            $routeService = request()->route('service');
            if ($routeService !== null) {
                $serviceId = is_object($routeService) && method_exists($routeService, 'getKey')
                    ? (int) $routeService->getKey()
                    : (int) $routeService;
            }
        }

        if ($serviceId > 0) {
            $this->service = Service::query()
                ->with(['images'])
                ->findOrFail($serviceId);

            $this->authorize('update', $this->service);

            $this->title = $this->service->title;
            $this->slug = $this->service->slug;
            $this->description = (string) $this->service->description;
            $this->excerpt = (string) ($this->service->excerpt ?? '');
            $this->price = $this->service->price !== null ? (string) $this->service->price : null;
            $this->status = $this->service->status->value;
            $this->featured = (bool) $this->service->featured;
            $this->order = (int) $this->service->order;
            $this->title_seo = (string) ($this->service->title_seo ?? '');

            foreach ($this->service->images as $image) {
                $this->galleryImages[] = [
                    'path' => $image->image_path,
                    'order' => $image->order,
                    'is_cover' => $image->is_cover,
                    'width' => $image->width,
                    'height' => $image->height,
                ];
            }
        } else {
            $this->authorize('create', Service::class);
        }
    }

    public function updatedTitle(string $value): void
    {
        if ($this->service === null) {
            $this->slug = Str::slug($value);
        }
    }

    public function regenerateSlug(): void
    {
        if ($this->title === '') {
            $this->addError('title', __('Title is required to generate a slug.'));

            return;
        }

        $this->slug = SlugGenerator::unique(Service::class, $this->title, $this->service?->id);
    }

    public function save(): void
    {
        $validated = $this->validate($this->rules());

        $data = [
            'title' => $validated['title'],
            'slug' => $validated['slug'],
            'description' => $validated['description'],
            'excerpt' => $validated['excerpt'] ?: null,
            'price' => $validated['price'] !== null && $validated['price'] !== '' ? (float) $validated['price'] : null,
            'status' => $validated['status'],
            'featured' => (bool) ($validated['featured'] ?? false),
            'order' => (int) ($validated['order'] ?? 0),
            'title_seo' => $validated['title_seo'] ?: null,
        ];

        $this->service = app(SaveService::class)->handle(
            service: $this->service,
            data: $data,
            galleryImages: $this->galleryImages,
        );

        FluxFacade::toast(variant: 'success', text: __('Service saved.'));

        $this->redirectRoute('dashboard.services.index', navigate: true);
    }

    public function render(): View
    {
        return view('livewire.services.service-form')->title($this->service ? __('Edit service') : __('New service'));
    }

    /**
     * @return array<string, mixed>
     */
    private function rules(): array
    {
        $slugRules = ['required', 'string', 'max:255'];
        if ($this->service !== null) {
            $slugRules[] = Rule::unique('services', 'slug')->ignore($this->service->id);
        } else {
            $slugRules[] = Rule::unique('services', 'slug');
        }

        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => $slugRules,
            'description' => ['required', 'string', 'min:1'],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
            'featured' => ['boolean'],
            'order' => ['integer', 'min:0'],
            'title_seo' => ['nullable', 'string', 'max:255'],
        ];
    }
}
