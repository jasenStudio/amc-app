<?php

namespace App\Livewire\Tags;

use App\Models\Tag;
use App\Support\SlugGenerator;
use Flux\Flux as FluxFacade;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.dashboard')]
class TagForm extends Component
{
    public ?Tag $tag = null;

    public string $name = '';

    public string $slug = '';

    public bool $confirmingDeletion = false;

    public function mount(int $tagId = 0): void
    {
        if ($tagId === 0) {
            $routeTag = request()->route('tag');
            if ($routeTag !== null) {
                $tagId = is_object($routeTag) && method_exists($routeTag, 'getKey')
                    ? (int) $routeTag->getKey()
                    : (int) $routeTag;
            }
        }

        if ($tagId > 0) {
            $this->tag = Tag::findOrFail($tagId);

            $this->name = $this->tag->name;
            $this->slug = $this->tag->slug;
        }
    }

    public function updatedName(string $value): void
    {
        if ($this->tag === null) {
            $this->slug = Str::slug($value);
        }
    }

    public function regenerateSlug(): void
    {
        if ($this->name === '') {
            $this->addError('name', __('Name is required to generate a slug.'));

            return;
        }

        $this->slug = SlugGenerator::unique(Tag::class, $this->name, $this->tag?->id);
    }

    public function save(): void
    {
        $validated = $this->validate($this->rules());

        if ($this->tag !== null) {
            $this->tag->update($validated);
        } else {
            $this->tag = Tag::create($validated);
        }

        FluxFacade::toast(variant: 'success', text: __('Tag saved.'));

        $this->redirectRoute('dashboard.tags.index', navigate: true);
    }

    public function delete(): void
    {
        if ($this->tag === null) {
            return;
        }

        $this->tag->delete();

        FluxFacade::toast(variant: 'success', text: __('Tag deleted.'));

        $this->redirectRoute('dashboard.tags.index', navigate: true);
    }

    public function render(): View
    {
        return view('livewire.tags.tag-form', [
            'posts_count' => $this->tag?->posts()->count() ?? 0,
        ])->title($this->tag ? __('Edit tag') : __('New tag'));
    }

    /**
     * @return array<string, mixed>
     */
    private function rules(): array
    {
        $slugRules = ['required', 'string', 'max:255'];
        if ($this->tag !== null) {
            $slugRules[] = Rule::unique('tags', 'slug')->ignore($this->tag->id);
        } else {
            $slugRules[] = Rule::unique('tags', 'slug');
        }

        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => $slugRules,
        ];
    }
}
