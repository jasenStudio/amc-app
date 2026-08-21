<?php

use App\Models\Image;
use App\Models\Post;
use Illuminate\Database\Migrations\Migration;

/**
 * Migrate existing cover_image data from posts table to images table.
 */
return new class extends Migration
{
    public function up(): void
    {
        $posts = Post::whereNotNull('cover_image')->get();

        foreach ($posts as $post) {
            $coverImage = $post->getAttribute('cover_image');
            $coverImageThumb = $post->getAttribute('cover_image_thumb');

            if ($coverImage && $coverImageThumb) {
                Image::create([
                    'imageable_type' => Post::class,
                    'imageable_id' => $post->id,
                    'thumb_path' => $coverImageThumb,
                    'full_path' => $coverImage,
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
