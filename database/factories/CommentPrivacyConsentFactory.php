<?php

namespace Database\Factories;

use App\Models\CommentPrivacyConsent;
use App\Models\Post;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CommentPrivacyConsent>
 */
class CommentPrivacyConsentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'commentable_type' => Post::class,
            'commentable_id' => Post::factory(),
            'commenter_name' => fake()->name(),
            'commenter_email' => fake()->safeEmail(),
            'ip' => fake()->ipv4(),
            'user_agent' => fake()->userAgent(),
            'accepted_at' => now(),
        ];
    }
}
