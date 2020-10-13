<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PageTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testHomePage()
    {
        $response = $this->get('/home');
        $response->assertStatus(302);
    }

    public function testDashboardPage()
    {
        $response = $this->get('/dashboard');
        $response->assertStatus(200);
    }
}
