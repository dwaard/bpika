<?php

namespace Tests\Feature;

use Tests\TestCase;

class PageTest extends TestCase
{
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
