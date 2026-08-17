<?php

namespace Tests\Feature;

use Tests\TestCase;

class ServicesSectionTest extends TestCase
{
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

    public function test_services_cards_have_accessible_images_and_decorative_icons(): void
    {
        $response = $this->get('/');

        $response
            ->assertSee('alt="Instalación de puntos de anclaje"', false)
            ->assertSee('alt="Líneas de vida certificadas"', false)
            ->assertSee('aria-hidden="true">&rarr;</span>', false);
    }

    public function test_services_cards_link_to_their_detail_pages(): void
    {
        $response = $this->get('/');

        $response
            ->assertSee('href="'.route('services.show', 'puntos-de-anclaje').'"', false)
            ->assertSee('focus-visible:ring-4 focus-visible:ring-amc-orange', false);
    }

    public function test_services_card_keeps_text_and_arrow_in_the_same_flex_row(): void
    {
        $response = $this->get('/');

        $response
            ->assertSee('absolute inset-x-6 bottom-6 flex items-end gap-2', false)
            ->assertSee('min-w-0 flex-1', false)
            ->assertSee('shrink-0 text-5xl', false);
    }

    public function test_services_card_descriptions_remain_available_in_full(): void
    {
        $response = $this->get('/');

        $response
            ->assertDontSee('line-clamp-2', false)
            ->assertSee('Instalación de puntos de anclaje certificados para trabajo seguro en alturas.');
    }

    public function test_services_heading_and_view_all_link_share_a_flex_row(): void
    {
        $response = $this->get('/');

        $response->assertSee('mb-8 flex items-center justify-between gap-4', false);
    }
}
