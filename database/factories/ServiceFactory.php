<?php

namespace Database\Factories;

use App\Enums\ActiveStatus;
use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Service>
 */
class ServiceFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->unique()->sentence(4);

        return [
            'title' => $title,
            'title_seo' => null,
            'description' => fake()->paragraphs(3, true),
            'excerpt' => fake()->paragraph(),
            'price' => fake()->optional(0.5)->randomFloat(2, 50, 5000),
            'status' => ActiveStatus::Active,
            'featured' => false,
            'order' => 0,
        ];
    }

    public function active(): static
    {
        return $this->state(fn (): array => [
            'status' => ActiveStatus::Active,
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (): array => [
            'status' => ActiveStatus::Inactive,
        ]);
    }

    public function featured(): static
    {
        return $this->state(fn (): array => [
            'featured' => true,
        ]);
    }
}
