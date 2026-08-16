<?php

namespace Tests\Feature;

use Tests\TestCase;

class HeroTest extends TestCase
{
    public function test_home_renders_the_hero_section(): void
    {
        $response = $this->get(route('home'));

        $response
            ->assertOk()
            ->assertSee('Certificación ONAC / Res. 4272')
            ->assertSee('Protegemos vidas mediante')
            ->assertSee('soluciones certificadas.')
            ->assertSee('La seguridad de su equipo es nuestra mayor responsabilidad.')
            ->assertSee('Solicitar asesoría')
            ->assertSee('Ver portafolio');
    }

    public function test_hero_section_exposes_accessible_structure(): void
    {
        $response = $this->get(route('home'));

        $response
            ->assertSee('id="hero"', false)
            ->assertSee('aria-labelledby="hero-accessible-title"', false)
            ->assertSee('id="hero-accessible-title"', false);
    }

    public function test_hero_gradient_is_hidden_from_assistive_technology(): void
    {
        $response = $this->get(route('home'));

        $response->assertSee('hero-gradient absolute inset-0" aria-hidden="true"', false);
    }

    public function test_hero_picture_contains_a_single_image(): void
    {
        $html = $this->get(route('home'))->getContent();

        preg_match('/<picture[^>]*>(.*?)<\/picture>/s', $html, $matches);
        $this->assertNotEmpty($matches, 'The hero must render a <picture> element.');

        $imgCount = substr_count($matches[1], '<img');
        $this->assertSame(1, $imgCount, 'The hero <picture> must contain exactly one <img> fallback.');
    }

    public function test_hero_ctas_link_to_the_expected_sections(): void
    {
        $response = $this->get(route('home'));

        $response
            ->assertSee('href="#services"', false)
            ->assertSee('href="#contact"', false);
    }

    public function test_hero_image_is_flagged_as_lcp(): void
    {
        $response = $this->get(route('home'));

        $response->assertSee('fetchpriority="high"', false);
    }

    public function test_hero_ctas_have_visible_focus_indicator(): void
    {
        $response = $this->get(route('home'));

        $response
            ->assertSee('focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-offset-2 focus-visible:ring-offset-amc-blue', false);
    }
}
