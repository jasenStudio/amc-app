<?php

use App\Models\Image;
use App\Models\Post;

/**
 * Migrate existing cover_image data from posts table to images table.
 */
return new class extends \Illuminate\Database\Migrations\Migration
{
    public function up(): void
    {
        $posts = Post::whereNotNull('cover_image')->get();

        foreach ($posts as $post) {
            if ($post->cover_image && $post->cover_image_thumb) {
                Image::create([
                    'imageable_type' => Post::class,
                    'imageable_id' => $post->id,
                    'thumb_path' => $post->cover_image_thumb,
                    'full_path' => $post->cover_image,
                    'order' => 0,
                ]);
            }
        }
    }

    public function down(): void
    {
        Image::where('imageable_type', Post::class)->delete();
    }
};
