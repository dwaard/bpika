<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\App;
use Tests\TestCase;

class AdminCreateTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function testCreateUserShouldSeeInDatabase()
    {
        // Set locale for the strings
        App::setLocale('en');
        $username = 'Taylor Otwell';
        $email = 'taylor@bpika.nl';
        $this->artisan('admin:create')
            ->expectsQuestion('Name', $username)
            ->expectsQuestion('E-Mail Address', $email)
            ->expectsQuestion('Password', '12345678')
            ->expectsQuestion('Confirm Password', '12345678')
            ->expectsOutput("Admin user $username is created")
            ->assertExitCode(0);

        $this->assertDatabaseHas('users', [
            'name' => $username,
            'email' => $email
        ]);

    }

}
