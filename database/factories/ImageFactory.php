<?php

namespace Database\Factories;

use App\Models\Image;
use App\Models\Post;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Image>
 */
class ImageFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'imageable_type' => Post::class,
            'imageable_id' => Post::factory(),
            'thumb_path' => 'images/thumb/'.fake()->uuid().'.webp',
            'full_path' => 'images/full/'.fake()->uuid().'.webp',
            'alt' => null,
            'order' => 0,
        ];
    }
}
