<?php

namespace App\Actions\Blog;

use App\Models\Post;
use App\Models\Tag;
use App\Support\HtmlSanitizer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use InvalidArgumentException;

class SavePost
{
    public function __construct(
        private HtmlSanitizer $sanitizer,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     * @param  array<int, int|string>  $tagIds
     */
    public function handle(
        ?Post $post,
        array $data,
        int $authorId,
        array $tagIds,
        ?string $newTagName,
        bool $shouldRemoveCover,
        ?string $coverThumbPath,
        ?string $coverFullPath,
    ): Post {
        $cleanBody = $this->sanitizer->clean($data['body'] ?? '');

        if ($cleanBody === '') {
            throw new InvalidArgumentException(
                __('The post body cannot be empty after sanitization.')
            );
        }

        $data['body'] = $cleanBody;

        return DB::transaction(function () use ($post, $data, $authorId, $tagIds, $newTagName, $shouldRemoveCover, $coverThumbPath, $coverFullPath): Post {
            if ($post !== null) {
                $post->update($data);

                if ($shouldRemoveCover) {
                    if ($post->coverImage) {
                        $post->coverImage->delete();
                    }
                } elseif ($coverFullPath) {
                    if ($post->coverImage) {
                        $post->coverImage->delete();
                    }
                    $post->coverImage()->create([
                        'thumb_path' => $coverThumbPath,
                        'full_path' => $coverFullPath,
                        'order' => 0,
                    ]);
                }
            } else {
                $data['author_id'] = $authorId;
                $post = Post::create($data);

                if ($coverFullPath) {
                    $post->coverImage()->create([
                        'thumb_path' => $coverThumbPath,
                        'full_path' => $coverFullPath,
                        'order' => 0,
                    ]);
                }
            }

            $resolvedTagIds = $tagIds;
            $trimmedName = trim($newTagName ?? '');
            if ($trimmedName !== '') {
                $tag = Tag::firstOrCreate(
                    ['slug' => Str::slug($trimmedName)],
                    ['name' => $trimmedName]
                );
                $resolvedTagIds[] = $tag->id;
            }
            $resolvedTagIds = array_values(array_unique(array_map('intval', $resolvedTagIds)));
            $post->tags()->sync($resolvedTagIds);

            return $post;
        });
    }
}
