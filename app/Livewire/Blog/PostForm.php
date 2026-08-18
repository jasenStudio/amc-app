<?php

namespace App\Livewire\Blog;

use App\Actions\Images\ConvertImageToWebp;
use App\Models\Post;
use App\Models\Tag;
use App\Support\HtmlSanitizer;
use Flux\Flux as FluxFacade;
use Illuminate\Contracts\View\View;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
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

    public ?UploadedFile $cover_upload = null;

    public bool $should_remove_cover = false;

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
                ->with('tags')
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

    public function removeCover(): void
    {
        $this->should_remove_cover = true;
        $this->cover_upload = null;
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
            'cover_upload' => ['nullable', 'image', 'max:4096'],
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

            $coverThumb = $this->post?->cover_image_thumb;
            $coverFull = $this->post?->cover_image;

            if ($this->cover_upload instanceof UploadedFile) {
                $paths = app(ConvertImageToWebp::class)($this->cover_upload, 'blog/webp', 'public');
                $coverThumb = $paths['thumb'];
                $coverFull = $paths['full'];

                // Clean up previous cover when replacing.
                if ($this->post !== null && $this->post->cover_image_thumb) {
                    app(ConvertImageToWebp::class)
                        ->delete((string) $this->post->cover_image_thumb, (string) $this->post->cover_image);
                }
            } elseif ($this->should_remove_cover) {
                if ($this->post !== null) {
                    app(ConvertImageToWebp::class)
                        ->delete((string) $this->post->cover_image_thumb, (string) $this->post->cover_image);
                }
                $coverThumb = null;
                $coverFull = null;
            }

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
                'cover_image_thumb' => $coverThumb,
                'cover_image' => $coverFull,
            ];

            if ($this->post !== null) {
                $this->post->update($data);
            } else {
                $data['author_id'] = $user->id;
                $this->post = Post::create($data);
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
        ]);
    }
}
