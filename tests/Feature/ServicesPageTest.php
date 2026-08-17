<?php

namespace Tests\Feature;

use Tests\TestCase;

class ServicesPageTest extends TestCase
{
    public function test_services_index_renders_the_placeholder_catalog(): void
    {
        $response = $this->get('/services');

        $response
            ->assertOk()
            ->assertSee('Soluciones para trabajar con más seguridad.')
            ->assertSee('Instalación de Puntos de Anclaje')
            ->assertSee('Mantenimiento y Recertificación');
    }

    public function test_services_index_renders_six_service_cards(): void
    {
        $html = $this->get('/services')->getContent();

        $this->assertSame(6, substr_count($html, '<article'));
    }

    public function test_service_show_renders_a_valid_placeholder_service(): void
    {
        $response = $this->get('/services/puntos-de-anclaje');

        $response
            ->assertOk()
            ->assertSee('Instalación de Puntos de Anclaje')
            ->assertSee('Volver a servicios')
            ->assertSee('Solicitar asesoría');
    }

    public function test_service_show_returns_not_found_for_an_unknown_slug(): void
    {
        $this->get('/services/no-existe')->assertNotFound();
    }

    public function test_service_cards_link_to_the_show_route(): void
    {
        $response = $this->get('/services');

        $response->assertSee('href="'.route('services.show', 'lineas-de-vida-certificadas').'"', false);
    }
}
