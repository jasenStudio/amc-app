<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FooterTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_renders_the_footer(): void
    {
        $response = $this->get(route('home'));

        $response
            ->assertOk()
            ->assertSee('AMC Gestión de Riesgos SAS', false)
            ->assertSee('+573147874006', false)
            ->assertSee('mailto:gerencia@amcgestiondelriesgo.com.co', false)
            ->assertSee('Navegación', false)
            ->assertSee('Portafolio y Asesoría', false);
    }

    public function test_footer_contains_all_navbar_links(): void
    {
        $response = $this->get(route('home'));

        $response
            ->assertSee('href="'.route('home').'"', false)
            ->assertSee('href="'.route('home').'#about"', false)
            ->assertSee('href="'.route('home').'#services"', false)
            ->assertSee('href="'.route('home').'#contact"', false)
            ->assertSee('href="'.route('projects').'"', false)
            ->assertSee('href="'.route('blog').'"', false);
    }

    public function test_footer_contains_social_icons(): void
    {
        $response = $this->get(route('home'));

        $response
            ->assertSee('aria-label="YouTube"', false);
    }

    public function test_footer_contains_portafolio_link(): void
    {
        $response = $this->get(route('home'));

        $response
            ->assertSee('href="'.url('/portafolio').'"', false)
            ->assertSee('target="_blank"', false)
            ->assertSee('rel="noopener noreferrer"', false);
    }

    public function test_footer_renders_on_about_pages(): void
    {
        $response = $this->get(route('about.vision'));

        $response
            ->assertOk()
            ->assertSee('AMC Gestión de Riesgos SAS', false)
            ->assertSee('Navegación', false);
    }

    public function test_footer_renders_on_services_pages(): void
    {
        $response = $this->get(route('services'));

        $response
            ->assertOk()
            ->assertSee('AMC Gestión de Riesgos SAS', false)
            ->assertSee('Navegación', false);
    }

    public function test_footer_renders_on_projects_pages(): void
    {
        $response = $this->get(route('projects'));

        $response
            ->assertOk()
            ->assertSee('AMC Gestión de Riesgos SAS', false)
            ->assertSee('Navegación', false);
    }

    public function test_footer_renders_on_blog_pages(): void
    {
        $response = $this->get(route('blog'));

        $response
            ->assertOk()
            ->assertSee('AMC Gestión de Riesgos SAS', false)
            ->assertSee('Navegación', false);
    }

    public function test_footer_contains_privacy_policy_link(): void
    {
        $response = $this->get(route('home'));

        $response
            ->assertSee('href="'.route('privacy.policy').'"', false)
            ->assertSee('Política de Tratamiento de Datos Personales', false);
    }
}
