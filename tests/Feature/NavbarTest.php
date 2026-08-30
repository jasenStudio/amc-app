<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\Project;
use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NavbarTest extends TestCase
{
    use RefreshDatabase;

    protected function seedFeaturedContent(): void
    {
        Service::factory()->create(['status' => 'active', 'featured' => true]);
        Project::factory()->active()->featured()->create();
        Post::factory()->published()->featured()->create();
    }

    public function test_home_renders_the_responsive_navigation(): void
    {
        $this->seedFeaturedContent();

        $response = $this->get(route('home'));

        $response
            ->assertOk()
            ->assertSee('Gestión de riesgos SAS')
            ->assertSee('Inicio')
            ->assertSee('Servicios')
            ->assertSee('Nosotros')
            ->assertSee('Contacto')
            ->assertSee('COTIZAR AHORA')
            ->assertSee('Navegación principal')
            ->assertSee('Navegación móvil');
    }

    public function test_navigation_exposes_accessible_active_and_toggle_states(): void
    {
        $response = $this->get(route('home'));

        $response
            ->assertSee("x-bind:aria-current=\"activeSection === 'hero' ? 'page' : null\"", false)
            ->assertSee('aria-controls="mobile-navigation"', false)
            ->assertSee(':aria-expanded="open.toString()"', false)
            ->assertSee('@keydown.escape.window="open = false"', false)
            ->assertSee('x-show="open"', false)
            ->assertSee('x-transition:enter-start="-translate-x-full"', false)
            ->assertSee('role="dialog"', false)
            ->assertSee('aria-modal="true"', false)
            ->assertSee('x-bind:aria-current="activeSection ===', false)
            ->assertSee('x-bind:aria-current="activeSection === \'about\'', false)
            ->assertSee("activeSection === 'about'", false)
            ->assertSee('x-data="navbar(', false)
            ->assertSee('href="#hero"', false)
            ->assertSee('id="hero"', false)
            ->assertSee('id="navbar-dropdown-nosotros-desktop-button"', false)
            ->assertSee('@click="dropdownOpen = !dropdownOpen"', false)
            ->assertSee('@click.outside="dropdownOpen = false"', false)
            ->assertSee('@keydown.escape.stop="dropdownOpen = false"', false)
            ->assertSee('role="menu"', false)
            ->assertDontSee('@mouseenter="dropdownOpen = true"', false)
            ->assertDontSee('<style>', false);
    }

    public function test_home_sections_link_to_their_dedicated_pages(): void
    {
        $this->seedFeaturedContent();

        $response = $this->get(route('home'));

        $response
            ->assertSee('href="'.route('services').'"', false)
            ->assertSee('href="'.route('projects').'"', false)
            ->assertSee('href="'.route('blog').'"', false)
            ->assertSee('href="'.route('home').'#about"', false)
            ->assertSee('href="'.route('home').'#services"', false)
            ->assertSee('href="'.route('home').'#projects"', false)
            ->assertSee('href="'.route('home').'#blog"', false)
            ->assertSee('href="'.route('home').'#contact"', false);
    }

    public function test_dropdown_parent_and_children_are_active_on_about_pages(): void
    {
        $response = $this->get(route('about.vision'));

        $response
            ->assertOk()
            ->assertSee('aria-current="page"', false)
            ->assertSee('href="'.route('about.vision').'"', false)
            ->assertSee('href="'.route('about.mission').'"', false)
            ->assertSee('Mostrar opciones de Nosotros');
    }

    public function test_placeholder_pages_are_navigable(): void
    {
        foreach (['services', 'projects', 'blog', 'about.vision', 'about.mission'] as $routeName) {
            $this->get(route($routeName))->assertOk();
        }
    }

    public function test_inicio_returns_to_home_when_rendered_outside_home(): void
    {
        $response = $this->get(route('blog'));

        $response
            ->assertOk()
            ->assertSee('href="'.route('home').'"', false)
            ->assertDontSee('href="#hero"', false);
    }

    public function test_navbar_hides_section_links_when_no_featured_content(): void
    {
        $response = $this->get(route('home'));

        $response
            ->assertOk()
            ->assertSee('href="'.route('home').'#about"', false)
            ->assertSee('href="'.route('home').'#contact"', false)
            ->assertDontSee('href="'.route('home').'#services"', false)
            ->assertDontSee('href="'.route('home').'#projects"', false)
            ->assertDontSee('href="'.route('home').'#blog"', false);
    }

    public function test_navbar_shows_section_links_when_featured_content_exists(): void
    {
        $this->seedFeaturedContent();

        $response = $this->get(route('home'));

        $response
            ->assertOk()
            ->assertSee('href="'.route('home').'#services"', false)
            ->assertSee('href="'.route('home').'#projects"', false)
            ->assertSee('href="'.route('home').'#blog"', false);
    }
}
