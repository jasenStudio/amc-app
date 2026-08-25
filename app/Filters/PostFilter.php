<?php

namespace App\Filters;

use App\Enums\UserRole;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class PostFilter
{
    public function __construct(
        public string $search = '',
        public string $status = '',
        public string $featured = '',
        public string $tag = '',
    ) {}

    /**
     * @return Builder<Post>
     */
    public function apply(): Builder
    {
        $query = Post::query()
            ->with(['author', 'tags', 'coverImage'])
            ->orderByDesc('id');

        $user = Auth::user();

        if (! $user instanceof User || ! in_array($user->role, [UserRole::SuperAdmin, UserRole::Admin], true)) {
            $query->where('author_id', $user?->getAuthIdentifier());
        }

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
}
