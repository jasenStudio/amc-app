<?php

namespace App\Livewire\Blog;

use App\Models\Post;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Schema;
use Livewire\Component;

class FeaturedPosts extends Component
{
    public function render(): View
    {
        $posts = collect();

        if (Schema::hasTable('posts')) {
            $posts = Post::query()
                ->published()
                ->featured()
                ->ordered()
                ->with('tags')
                ->limit(3)
                ->get();
        }

        return view('livewire.blog.featured-posts', [
            'posts' => $posts,
        ]);
    }
}
