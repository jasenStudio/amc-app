<?php

namespace App\Livewire\Blog;

use App\Models\Post;
use App\Models\Tag;
use App\Support\HtmlSanitizer;
use App\Support\ImageUrl;
use Flux\Flux as FluxFacade;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.dashboard')]
class PostForm extends Component
{
    use WithFileUploads;

    public ?Post $post = null;

    public string $title = '';

    public string $slug = '';

    public string $excerpt = '';

    public string $body = '';

    public string $status = 'draft';

    public ?string $published_at = null;

    public bool $featured = false;

    public int $order = 0;

    public string $seo_title = '';

    public string $seo_description = '';

    public ?string $seo_image = null;

    /** @var array<int, string> */
    public array $tag_ids = [];

    public string $new_tag_name = '';

    public ?string $coverImageThumb = null;

    public ?string $coverImageFull = null;

    public ?string $coverImageThumbPath = null;

    public ?string $coverImageFullPath = null;

    public bool $shouldRemoveCover = false;

    public function mount(int $postId = 0): void
    {
        if ($postId === 0) {
            $routePost = request()->route('post');
            if ($routePost !== null) {
                $postId = is_object($routePost) && method_exists($routePost, 'getKey')
                    ? (int) $routePost->getKey()
                    : (int) $routePost;
            }
        }

        if ($postId > 0) {
            $this->post = Post::query()
                ->with(['tags', 'coverImage'])
                ->findOrFail($postId);

            $this->authorize('update', $this->post);

            $this->title = $this->post->title;
            $this->slug = $this->post->slug;
            $this->excerpt = (string) ($this->post->excerpt ?? '');
            $this->body = (string) $this->post->body;
            $this->status = $this->post->status->value;
            $this->published_at = optional($this->post->published_at)->format('Y-m-d\TH:i');
            $this->featured = (bool) $this->post->featured;
            $this->order = (int) $this->post->order;
            $this->seo_title = (string) ($this->post->seo_title ?? '');
            $this->seo_description = (string) ($this->post->seo_description ?? '');
            $this->seo_image = $this->post->seo_image;
            $this->tag_ids = $this->post->tags->pluck('id')->map(fn ($id) => (string) $id)->all();

            if ($this->post->coverImage) {
                $this->coverImageThumb = ImageUrl::public($this->post->coverImage->thumb_path);
                $this->coverImageFull = ImageUrl::public($this->post->coverImage->full_path);
            }
        } else {
            $this->authorize('create', Post::class);
        }
    }

    public function updatedTitle(string $value): void
    {
        if ($this->post === null) {
            $this->slug = Str::slug($value);
        }
    }

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

    public function regenerateSlug(): void
    {
        if ($this->title === '') {
            $this->addError('title', __('Title is required to generate a slug.'));

            return;
        }

        $base = Str::slug($this->title) ?: 'n-a';
        $slug = $base;
        $suffix = 1;

        $exists = Post::query()
            ->where('slug', $slug)
            ->when($this->post !== null, fn ($q) => $q->where('id', '!=', $this->post->getKey()))
            ->withTrashed()
            ->exists();

        while ($exists) {
            $slug = $base.'-'.$suffix++;
            $exists = Post::query()
                ->where('slug', $slug)
                ->when($this->post !== null, fn ($q) => $q->where('id', '!=', $this->post->getKey()))
                ->withTrashed()
                ->exists();
        }

        $this->slug = $slug;
    }

    public function save(): void
    {
        $slugRules = ['required', 'string', 'max:255'];
        if ($this->post !== null) {
            $slugRules[] = Rule::unique('posts', 'slug')->ignore($this->post->id);
        } else {
            $slugRules[] = Rule::unique('posts', 'slug');
        }

        $rules = [
            'title' => ['required', 'string', 'max:255'],
            'slug' => $slugRules,
            'excerpt' => ['nullable', 'string', 'max:500'],
            'body' => ['required', 'string', 'min:1'],
            'status' => ['required', Rule::in(['draft', 'published'])],
            'published_at' => ['nullable', 'date'],
            'featured' => ['boolean'],
            'order' => ['integer', 'min:0'],
            'seo_title' => ['nullable', 'string', 'max:255'],
            'seo_description' => ['nullable', 'string', 'max:500'],
            'seo_image' => ['nullable', 'string', 'max:255'],
            'tag_ids' => ['array'],
            'tag_ids.*' => ['integer', 'exists:tags,id'],
            'new_tag_name' => ['nullable', 'string', 'max:50'],
        ];

        $validated = $this->validate($rules);

        $sanitizer = app(HtmlSanitizer::class);
        $cleanBody = $sanitizer->clean($validated['body']);

        if ($cleanBody === '') {
            $this->addError('body', __('The post body cannot be empty after sanitization.'));

            return;
        }

        DB::transaction(function () use ($validated, $cleanBody): void {
            $user = Auth::user();

            $data = [
                'title' => $validated['title'],
                'slug' => $validated['slug'],
                'excerpt' => $validated['excerpt'] ?? null,
                'body' => $cleanBody,
                'status' => $validated['status'],
                'published_at' => $validated['published_at'] ?? null,
                'featured' => (bool) ($validated['featured'] ?? false),
                'order' => (int) ($validated['order'] ?? 0),
                'seo_title' => $validated['seo_title'] ?: null,
                'seo_description' => $validated['seo_description'] ?: null,
                'seo_image' => $validated['seo_image'] ?: null,
            ];

            if ($this->post !== null) {
                $this->post->update($data);

                if ($this->shouldRemoveCover) {
                    if ($this->post->coverImage) {
                        $this->post->coverImage->delete();
                    }
                } elseif ($this->coverImageFullPath) {
                    if ($this->post->coverImage) {
                        $this->post->coverImage->delete();
                    }
                    $this->post->coverImage()->create([
                        'thumb_path' => $this->coverImageThumbPath,
                        'full_path' => $this->coverImageFullPath,
                        'order' => 0,
                    ]);
                }
            } else {
                $data['author_id'] = $user->id;
                $this->post = Post::create($data);

                if ($this->coverImageFullPath) {
                    $this->post->coverImage()->create([
                        'thumb_path' => $this->coverImageThumbPath,
                        'full_path' => $this->coverImageFullPath,
                        'order' => 0,
                    ]);
                }
            }

            // Tag attach: combine existing selection with newly created tags.
            $tagIds = $validated['tag_ids'] ?? [];
            $newName = trim($validated['new_tag_name'] ?? '');
            if ($newName !== '') {
                $tag = Tag::firstOrCreate(
                    ['slug' => Str::slug($newName)],
                    ['name' => $newName]
                );
                $tagIds[] = $tag->id;
            }
            $tagIds = array_values(array_unique(array_map('intval', $tagIds)));
            $this->post->tags()->sync($tagIds);
        });

        FluxFacade::toast(variant: 'success', text: __('Post saved.'));

        $this->redirectRoute('blog.index', navigate: true);
    }

    public function render(): View
    {
        return view('livewire.blog.post-form', [
            'all_tags' => Tag::query()->orderBy('name')->get(),
            'author' => Auth::user(),
        ])->title($this->post ? __('Edit post') : __('New post'));
    }
}
