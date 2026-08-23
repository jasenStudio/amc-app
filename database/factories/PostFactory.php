<?php

namespace Database\Factories;

use App\Enums\PostStatus;
use App\Models\Post;
use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Post>
 */
class PostFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->unique()->sentence(6);

        return [
            'title' => $title,
            'excerpt' => fake()->paragraph(),
            'body' => fake()->paragraphs(3, true),
            'status' => PostStatus::Draft,
            'published_at' => null,
            'author_id' => User::factory(),
            'featured' => false,
            'order' => 0,
            'seo_title' => null,
            'seo_description' => null,
            'seo_image' => null,
        ];
    }

    public function published(?CarbonInterface $at = null): static
    {
        return $this->state(fn (): array => [
            'status' => PostStatus::Published,
            'published_at' => $at ?? now()->subMinute(),
        ]);
    }

    public function featured(): static
    {
        return $this->state(fn (): array => [
            'featured' => true,
        ]);
    }

    public function scheduled(): static
    {
        return $this->state(fn (): array => [
            'status' => PostStatus::Published,
            'published_at' => now()->addDays(7),
        ]);
    }
}
