<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotFoundPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_404_hides_catalog_links_when_no_featured_content(): void
    {
        $this->get('/non-existent-page')
            ->assertNotFound()
            ->assertSee('Página no encontrada')
            ->assertDontSee('Ver servicios', false)
            ->assertDontSee('Ir al blog', false);
    }

    public function test_404_shows_catalog_links_when_featured_content_exists(): void
    {
        Service::factory()->create(['status' => 'active', 'featured' => true]);
        Post::factory()->published()->featured()->create();

        $this->get('/non-existent-page')
            ->assertNotFound()
            ->assertSee('Página no encontrada')
            ->assertSee('Ver servicios', false)
            ->assertSee('Ir al blog', false);
    }
}
