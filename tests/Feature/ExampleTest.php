<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_renders_the_fitness_journey(): void
    {
        $response = $this->get('/');

        $response
            ->assertOk()
            ->assertSee('Bukan cuma.')
            ->assertSee('Calculate Your BMI Now')
            ->assertSee('Perjalananmu')
            ->assertSee('data-floating-actions', false)
            ->assertSee('Lihat paket membership')
            ->assertSee('Buka bantuan cepat')
            ->assertSee(route('membership.index'), false)
            ->assertSee(route('home').'#faq', false);
    }
}
