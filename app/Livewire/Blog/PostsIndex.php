<?php

namespace App\Livewire\Blog;

use App\Enums\PostStatus;
use App\Filters\PostFilter;
use App\Livewire\Concerns\WithFilters;
use App\Models\Post;
use App\Models\Tag;
use Flux\Flux as FluxFacade;
use Illuminate\Contracts\View\View;
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
    use WithFilters, WithPagination;

    #[Url(except: '')]
    public string $search = '';

    #[Url(except: '')]
    public string $status = '';

    #[Url(except: '')]
    public string $featured = '';

    #[Url(except: '')]
    public string $tag = '';

    public ?int $confirmingDeletion = null;

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
        $filter = new PostFilter($this->search, $this->status, $this->featured, $this->tag);

        return view('livewire.blog.posts-index', [
            'posts' => $filter->apply()->paginate(15),
            'tags' => Tag::query()->orderBy('name')->get(),
            'statuses' => [PostStatus::Draft, PostStatus::Published],
        ]);
    }
}
