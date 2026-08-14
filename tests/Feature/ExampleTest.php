<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_returns_a_successful_response(): void
    {
        $response = $this->get(route('home'));

        $response
            ->assertOk()
            ->assertSee('Protegemos vidas mediante')
            ->assertSee('soluciones certificadas')
            ->assertSee('amc-blue')
            ->assertDontSee('flux:')
            ->assertDontSee('dark:');
    }
}
