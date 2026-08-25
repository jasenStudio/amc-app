<?php

namespace Tests\Feature;

use App\Models\Service;
use Database\Seeders\ServiceSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServicesSectionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        (new ServiceSeeder)->run();
    }

    public function test_home_renders_the_services_section(): void
    {
        $response = $this->get('/');

        $response
            ->assertOk()
            ->assertSee('Servicios Principales')
            ->assertSee('Instalación de Puntos de Anclaje')
            ->assertSee('Líneas de Vida Certificadas')
            ->assertSee('Capacitación y Entrenamiento')
            ->assertSee('Asesoría en Gestión de Riesgos');
    }

    public function test_services_section_exposes_accessible_structure(): void
    {
        $response = $this->get('/');

        $response
            ->assertSee('id="services"', false)
            ->assertSee('aria-labelledby="services-title"', false)
            ->assertSee('id="services-title"', false);
    }

    public function test_services_section_renders_six_cards(): void
    {
        $html = $this->get('/')->getContent();

        preg_match('/<section id="services".*?<\/section>/s', $html, $matches);
        $this->assertNotEmpty($matches, 'The services section must be rendered.');
        $this->assertSame(6, substr_count($matches[0], '<article'));
    }

    public function test_services_section_renders_the_view_all_link(): void
    {
        $response = $this->get('/');

        $response
            ->assertSee('Ver todos')
            ->assertSee('href="'.route('services').'"', false);
    }

    public function test_services_cards_link_to_their_detail_pages(): void
    {
        $service = Service::query()->where('title', 'Instalación de Puntos de Anclaje')->first();

        $response = $this->get('/');

        $response
            ->assertSee('href="'.route('services.show', $service->slug).'"', false);
    }
}
