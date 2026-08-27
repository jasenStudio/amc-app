<?php

namespace Tests\Feature;

use App\Models\Service;
use Database\Seeders\ServiceSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServicesPageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        (new ServiceSeeder)->run();
    }

    public function test_services_index_renders_the_catalog(): void
    {
        $response = $this->get('/servicios');

        $response
            ->assertOk()
            ->assertSee('Soluciones para trabajar con más seguridad.')
            ->assertSee('Instalación de Puntos de Anclaje')
            ->assertSee('Mantenimiento y Recertificación');
    }

    public function test_services_index_renders_six_service_cards(): void
    {
        $html = $this->get('/servicios')->getContent();

        $this->assertSame(6, substr_count($html, '<article'));
    }

    public function test_service_show_renders_a_valid_service(): void
    {
        $service = Service::query()->where('title', 'Instalación de Puntos de Anclaje')->first();

        $response = $this->get("/servicios/{$service->slug}");

        $response
            ->assertOk()
            ->assertSee('Instalación de Puntos de Anclaje')
            ->assertSee('Breadcrumb')
            ->assertSee('Servicios')
            ->assertSee('Solicitar asesoría');
    }

    public function test_service_show_returns_not_found_for_an_unknown_slug(): void
    {
        $this->get('/servicios/no-existe')->assertNotFound();
    }

    public function test_service_cards_link_to_the_show_route(): void
    {
        $service = Service::query()->where('title', 'Líneas de Vida Certificadas')->first();

        $response = $this->get('/servicios');

        $response->assertSee('href="'.route('services.show', $service->slug).'"', false);
    }

    public function test_service_show_does_not_render_price(): void
    {
        $service = Service::query()->where('title', 'Instalación de Puntos de Anclaje')->first();
        $service->update(['price' => 1500.00]);

        $response = $this->get("/servicios/{$service->slug}");

        $response
            ->assertOk()
            ->assertDontSee('1500')
            ->assertDontSee('1,500');
    }
}
