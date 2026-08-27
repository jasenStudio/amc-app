<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AboutSectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_about_page_renders_the_about_section(): void
    {
        $response = $this->get(route('home'));

        $response
            ->assertOk()
            ->assertSee('Detrás de cada línea de vida hay una familia esperando en casa.')
            ->assertSee('Personal competente')
            ->assertSee('Ingeniería documentada')
            ->assertSee('Respuesta en obra');
    }

    public function test_about_section_exposes_accessible_structure(): void
    {
        $response = $this->get(route('home'));

        $response
            ->assertSee('id="about"', false)
            ->assertSee('aria-labelledby="nosotros-title"', false)
            ->assertSee('id="nosotros-title"', false)
            ->assertSee('alt="Equipo de AMC Gestión de Riesgos en obra con elementos de protección personal"', false)
            ->assertSee('<ul', false)
            ->assertSee('<li', false);
    }

    public function test_home_uses_the_about_section_component(): void
    {
        $response = $this->get(route('home'));

        $response
            ->assertOk()
            ->assertSee('id="about"', false)
            ->assertSee('Detrás de cada línea de vida hay una familia esperando en casa.');
    }
}
