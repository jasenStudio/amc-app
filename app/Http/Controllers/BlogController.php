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

        $posts = collect();
        $tags = collect();

        if (Schema::hasTable('posts')) {
            $posts = Post::query()
                ->published()
                ->ordered()
                ->with('tags')
                ->when($tagSlug !== null && $tagSlug !== '', fn (Builder $q) => $q->whereHas('tags', fn (Builder $t) => $t->where('slug', $tagSlug)))
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
            ->with(['author', 'tags'])
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
