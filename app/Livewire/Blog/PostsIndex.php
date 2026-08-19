<?php

namespace App\Livewire\Blog;

use App\Models\Post;
use App\Models\Tag;
use App\PostStatus;
use Flux\Flux as FluxFacade;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.dashboard')]
#[Title('Blog')]
class PostsIndex extends Component
{
    use WithPagination;

    #[Url(except: '')]
    public string $search = '';

    #[Url(except: '')]
    public string $status = '';

    #[Url(except: '')]
    public string $featured = '';

    #[Url(except: '')]
    public string $tag = '';

    public ?int $confirmingDeletion = null;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatus(): void
    {
        $this->resetPage();
    }

    public function updatingFeatured(): void
    {
        $this->resetPage();
    }

    public function updatingTag(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->reset(['search', 'status', 'featured', 'tag']);
        $this->resetPage();
    }

    /**
     * @return Builder<Post>
     */
    private function buildQuery(): Builder
    {
        $query = Post::query()
            ->with(['author', 'tags', 'coverImage'])
            ->orderByDesc('id');

        if ($this->search !== '') {
            $term = '%'.$this->search.'%';
            $query->where(function (Builder $q) use ($term): void {
                $q->where('title', 'like', $term)
                    ->orWhereHas('tags', fn (Builder $t) => $t->where('name', 'like', $term));
            });
        }

        if ($this->status !== '') {
            $query->where('status', $this->status);
        }

        if ($this->featured === 'yes') {
            $query->where('featured', true);
        } elseif ($this->featured === 'no') {
            $query->where('featured', false);
        }

        if ($this->tag !== '') {
            $query->whereHas('tags', fn (Builder $q) => $q->where('slug', $this->tag));
        }

        return $query;
    }

    public function delete(int $postId): void
    {
        $post = Post::query()->findOrFail($postId);

        Gate::authorize('delete', $post);

        $post->delete();
        $this->confirmingDeletion = null;

        FluxFacade::toast(variant: 'success', text: __('Post deleted.'));
    }

    public function canUpdate(Post $post): bool
    {
        return Gate::allows('update', $post);
    }

    public function canDelete(Post $post): bool
    {
        return Gate::allows('delete', $post);
    }

    public function render(): View
    {
        return view('livewire.blog.posts-index', [
            'posts' => $this->buildQuery()->paginate(15),
            'tags' => Tag::query()->orderBy('name')->get(),
            'statuses' => [PostStatus::Draft, PostStatus::Published],
        ]);
    }
}
