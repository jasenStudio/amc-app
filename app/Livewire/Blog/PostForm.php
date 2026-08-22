<?php

namespace App\Livewire\Blog;

use App\Actions\Blog\SavePost;
use App\Livewire\Concerns\WithCoverImage;
use App\Models\Post;
use App\Models\Tag;
use App\Support\ImageUrl;
use App\Support\SlugGenerator;
use Flux\Flux as FluxFacade;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use InvalidArgumentException;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.dashboard')]
class PostForm extends Component
{
    use WithCoverImage;
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

    public function regenerateSlug(): void
    {
        if ($this->title === '') {
            $this->addError('title', __('Title is required to generate a slug.'));

            return;
        }

        $this->slug = SlugGenerator::unique(Post::class, $this->title, $this->post?->id);
    }

    public function save(): void
    {
        $validated = $this->validate($this->rules());

        $data = [
            'title' => $validated['title'],
            'slug' => $validated['slug'],
            'excerpt' => $validated['excerpt'] ?? null,
            'body' => $validated['body'],
            'status' => $validated['status'],
            'published_at' => $validated['published_at'] ?? null,
            'featured' => (bool) ($validated['featured'] ?? false),
            'order' => (int) ($validated['order'] ?? 0),
            'seo_title' => $validated['seo_title'] ?: null,
            'seo_description' => $validated['seo_description'] ?: null,
            'seo_image' => $validated['seo_image'] ?: null,
        ];

        try {
            $this->post = app(SavePost::class)->handle(
                post: $this->post,
                data: $data,
                authorId: Auth::id(),
                tagIds: $validated['tag_ids'] ?? [],
                newTagName: $validated['new_tag_name'] ?? null,
                shouldRemoveCover: $this->shouldRemoveCover,
                coverThumbPath: $this->coverImageThumbPath,
                coverFullPath: $this->coverImageFullPath,
            );
        } catch (InvalidArgumentException $e) {
            $this->addError('body', $e->getMessage());

            return;
        }

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

    /**
     * @return array<string, mixed>
     */
    private function rules(): array
    {
        $slugRules = ['required', 'string', 'max:255'];
        if ($this->post !== null) {
            $slugRules[] = Rule::unique('posts', 'slug')->ignore($this->post->id);
        } else {
            $slugRules[] = Rule::unique('posts', 'slug');
        }

        return [
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
    }
}
