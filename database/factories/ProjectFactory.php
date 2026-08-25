<?php

namespace Database\Factories;

use App\Enums\ActiveStatus;
use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Project>
 */
class ProjectFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->unique()->sentence(6);

        return [
            'title' => $title,
            'title_seo' => null,
            'description' => fake()->paragraphs(3, true),
            'excerpt' => fake()->paragraph(),
            'client' => fake()->company(),
            'location' => fake()->city(),
            'date' => fake()->dateTimeBetween('-2 years', 'now'),
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
