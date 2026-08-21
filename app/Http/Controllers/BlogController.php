<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Tag;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class BlogController extends Controller
{
    public function index(): View
    {
        $tagSlug = request('tag');
        $search = request('q');
        $date = request('date');

        $posts = collect();
        $tags = collect();

        if (Schema::hasTable('posts')) {
            $posts = Post::query()
                ->published()
                ->ordered()
                ->with(['tags', 'coverImage'])
                ->when($tagSlug !== null && $tagSlug !== '', fn (Builder $q) => $q->whereHas('tags', fn (Builder $t) => $t->where('slug', $tagSlug)))
                ->when($search !== null && $search !== '', fn (Builder $q) => $q->where(function (Builder $q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                        ->orWhere('excerpt', 'like', "%{$search}%");
                }))
                ->when($date !== null && $date !== '', fn (Builder $q) => $q->whereDate('published_at', $date))
                ->paginate(12)
                ->appends(request()->query());

            $tags = Tag::query()->orderBy('name')->get();
        }

        return view('pages::blog.index', [
            'posts' => $posts,
            'tags' => $tags,
            'activeTag' => $tagSlug,
        ]);
    }

    public function show(string $slug): View
    {
        $post = Post::query()
            ->published()
            ->with(['author', 'tags', 'coverImage'])
            ->where('slug', $slug)
            ->first();

        if ($post === null) {
            throw new NotFoundHttpException;
        }

        return view('pages::blog.show', [
            'post' => $post,
        ]);
    }
}
