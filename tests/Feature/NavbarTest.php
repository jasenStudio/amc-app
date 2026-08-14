<?php

namespace Tests\Feature;

use Tests\TestCase;

class NavbarTest extends TestCase
{
    public function test_home_renders_the_responsive_navigation(): void
    {
        $response = $this->get(route('home'));

        $response
            ->assertOk()
            ->assertSee('Gestión de riesgos SAS')
            ->assertSee('Inicio')
            ->assertSee('Servicios')
            ->assertSee('Nosotros')
            ->assertSee('Contacto')
            ->assertSee('Portafolio')
            ->assertSee('Navegación principal')
            ->assertSee('Navegación móvil');
    }

    public function test_navigation_exposes_accessible_active_and_toggle_states(): void
    {
        $response = $this->get(route('home'));

        $response
            ->assertSee('aria-current="page"', false)
            ->assertSee('aria-controls="mobile-navigation"', false)
            ->assertSee(':aria-expanded="open.toString()"', false)
            ->assertSee('@keydown.escape.window="open = false"', false)
            ->assertSee('x-show="open"', false)
            ->assertSee('x-transition:enter-start="-translate-x-full"', false)
            ->assertSee('role="dialog"', false)
            ->assertSee('aria-modal="true"', false);
    }
}
