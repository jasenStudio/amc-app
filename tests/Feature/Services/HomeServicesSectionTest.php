<?php

namespace Tests\Feature\Services;

use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomeServicesSectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_hides_services_section_when_no_featured_services(): void
    {
        Service::factory()->create(['status' => 'active', 'featured' => false]);

        $this->get(route('home'))
            ->assertOk()
            ->assertDontSee('id="services"', false)
            ->assertDontSee('Ver todos', false);
    }

    public function test_home_shows_services_section_when_featured_services_exist(): void
    {
        Service::factory()->create(['status' => 'active', 'featured' => true, 'title' => 'Featured Service']);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('id="services"', false)
            ->assertSee('Ver todos', false)
            ->assertSee('Featured Service');
    }
}
