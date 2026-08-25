<?php

namespace Tests\Feature\Services;

use App\Livewire\Services\FeaturedServices;
use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class FeaturedServicesTest extends TestCase
{
    use RefreshDatabase;

    public function test_renders_without_services(): void
    {
        Livewire::test(FeaturedServices::class)
            ->assertSee('No services available yet.');
    }

    public function test_renders_featured_services(): void
    {
        $featured = Service::factory()->create([
            'title' => 'Featured Service',
            'status' => 'active',
            'featured' => true,
        ]);

        $notFeatured = Service::factory()->create([
            'title' => 'Regular Service',
            'status' => 'active',
            'featured' => false,
        ]);

        Livewire::test(FeaturedServices::class)
            ->assertSee('Featured Service')
            ->assertDontSee('Regular Service');
    }

    public function test_does_not_render_inactive_services(): void
    {
        Service::factory()->create([
            'title' => 'Inactive Featured',
            'status' => 'inactive',
            'featured' => true,
        ]);

        Livewire::test(FeaturedServices::class)
            ->assertSee('No services available yet.');
    }

    public function test_respects_limit(): void
    {
        Service::factory(8)->create([
            'status' => 'active',
            'featured' => true,
        ]);

        $component = Livewire::test(FeaturedServices::class);

        $services = $component->viewData('services');
        $this->assertCount(6, $services);
    }

    public function test_uses_custom_limit(): void
    {
        Service::factory(4)->create([
            'status' => 'active',
            'featured' => true,
        ]);

        $component = Livewire::test(FeaturedServices::class, ['limit' => 2]);

        $services = $component->viewData('services');
        $this->assertCount(2, $services);
    }
}
