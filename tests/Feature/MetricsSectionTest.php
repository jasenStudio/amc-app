<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MetricsSectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_renders_the_metrics_section(): void
    {
        $response = $this->get(route('home'));

        $response
            ->assertOk()
            ->assertSee('10+')
            ->assertSee('500+')
            ->assertSee('5000+')
            ->assertSee('Años')
            ->assertSee('Proyectos')
            ->assertSee('Capacitados');
    }

    public function test_metrics_section_exposes_accessible_structure(): void
    {
        $response = $this->get(route('home'));

        $response
            ->assertSee('id="metrics"', false)
            ->assertSee('aria-labelledby="metrics-title"', false)
            ->assertSee('id="metrics-title"', false);
    }

    public function test_metrics_section_renders_three_cards(): void
    {
        $html = $this->get(route('home'))->getContent();

        $articleCount = substr_count($html, '<article');
        $this->assertGreaterThanOrEqual(3, $articleCount, 'The metrics section must render at least 3 <article> cards.');
    }

    public function test_metrics_icons_are_hidden_from_assistive_technology(): void
    {
        $response = $this->get(route('home'));

        $response
            ->assertSee('aria-hidden="true"', false);
    }

    public function test_metrics_icons_are_rendered_from_lucide(): void
    {
        $response = $this->get(route('home'));

        $response
            ->assertSee('data-slot="icon"', false);
    }
}
