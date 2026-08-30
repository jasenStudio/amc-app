<?php

namespace App\Livewire\Projects;

use App\Actions\Projects\SaveProject;
use App\Livewire\Concerns\WithGallery;
use App\Models\Project;
use App\Rules\ValidVideoUrl;
use App\Support\SlugGenerator;
use Flux\Flux as FluxFacade;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('layouts.dashboard')]
class ProjectForm extends Component
{
    use WithGallery;

    public ?Project $project = null;

    #[Validate]
    public string $title = '';

    #[Validate]
    public string $slug = '';

    #[Validate]
    public string $description = '';

    public string $excerpt = '';

    #[Validate]
    public string $client = '';

    public ?string $location = null;

    #[Validate]
    public string $date = '';

    #[Validate]
    public string $status = 'active';

    public bool $featured = false;

    public int $order = 0;

    public string $title_seo = '';

    #[Validate]
    public ?string $video_url = null;

    public function mount(int $projectId = 0): void
    {
        if ($projectId === 0) {
            $routeProject = request()->route('project');
            if ($routeProject !== null) {
                $projectId = is_object($routeProject) && method_exists($routeProject, 'getKey')
                    ? (int) $routeProject->getKey()
                    : (int) $routeProject;
            }
        }

        if ($projectId > 0) {
            $this->project = Project::query()
                ->with(['images'])
                ->findOrFail($projectId);

            $this->authorize('update', $this->project);

            $this->title = $this->project->title;
            $this->slug = $this->project->slug;
            $this->description = (string) $this->project->description;
            $this->excerpt = (string) ($this->project->excerpt ?? '');
            $this->client = (string) $this->project->client;
            $this->location = $this->project->location;
            $this->date = $this->project->date->format('Y-m-d');
            $this->status = $this->project->status->value;
            $this->featured = (bool) $this->project->featured;
            $this->order = (int) $this->project->order;
            $this->title_seo = (string) ($this->project->title_seo ?? '');
            $this->video_url = $this->project->video_url;

            foreach ($this->project->images as $image) {
                $this->galleryImages[] = [
                    'path' => $image->image_path,
                    'order' => $image->order,
                    'is_cover' => $image->is_cover,
                    'width' => $image->width,
                    'height' => $image->height,
                ];
            }
        } else {
            $this->authorize('create', Project::class);
        }
    }

    public function updatedTitle(string $value): void
    {
        if ($this->project === null) {
            $this->slug = Str::slug($value);
        }
    }

    public function regenerateSlug(): void
    {
        if ($this->title === '') {
            $this->addError('title', __('Title is required to generate a slug.'));

            return;
        }

        $this->slug = SlugGenerator::unique(Project::class, $this->title, $this->project?->id);
    }

    public function save(): void
    {
        $validated = $this->validate($this->rules());

        $data = [
            'title' => $validated['title'],
            'slug' => $validated['slug'],
            'description' => $validated['description'],
            'excerpt' => $validated['excerpt'] ?: null,
            'client' => $validated['client'],
            'location' => $validated['location'] ?: null,
            'date' => $validated['date'],
            'status' => $validated['status'],
            'featured' => (bool) ($validated['featured'] ?? false),
            'order' => (int) ($validated['order'] ?? 0),
            'title_seo' => $validated['title_seo'] ?: null,
            'video_url' => $validated['video_url'] ?: null,
        ];

        $this->project = app(SaveProject::class)->handle(
            project: $this->project,
            data: $data,
            galleryImages: $this->galleryImages,
        );

        FluxFacade::toast(variant: 'success', text: __('Project saved.'));

        $this->redirectRoute('dashboard.projects.index', navigate: true);
    }

    public function render(): View
    {
        return view('livewire.projects.project-form')->title($this->project ? __('Edit project') : __('New project'));
    }

    /**
     * @return array<string, mixed>
     */
    protected function rules(): array
    {
        $slugRules = ['required', 'string', 'max:255'];
        if ($this->project !== null) {
            $slugRules[] = Rule::unique('projects', 'slug')->ignore($this->project->id);
        } else {
            $slugRules[] = Rule::unique('projects', 'slug');
        }

        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => $slugRules,
            'description' => ['required', 'string', 'min:1'],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'client' => ['required', 'string', 'max:255'],
            'location' => ['nullable', 'string', 'max:255'],
            'date' => ['required', 'date'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
            'featured' => ['boolean'],
            'order' => ['integer', 'min:0'],
            'title_seo' => ['nullable', 'string', 'max:255'],
            'video_url' => ['nullable', 'url', new ValidVideoUrl],
        ];
    }
}
