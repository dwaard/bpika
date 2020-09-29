<?php

namespace Tests\Feature;

use App\Mail\UserInvited;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class UserInviteTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function testUnauthorizedShouldRedirectToLogin()
    {
        $response = $this->post('/users');

        $response->assertRedirect('login');
    }

    public function testStoreUserHappyPath()
    {
        // See exceptions in the console
        $this->withoutExceptionHandling();

        Mail::fake();

        $user = factory(User::class)->create();
        $user->markEmailAsVerified();

        $invitee = factory(User::class)->make();

        $response = $this->actingAs($user)
            ->post('/users', [
                '_token' => csrf_token(),
                'name' => $invitee->name,
                'email' => $invitee->email,
            ]);

        $this->assertNotEquals(500, $response->getStatusCode(),
            'Response returned status code 500');
        $response->assertSessionHas('success');
        $response->assertRedirect('/users');

        Mail::assertSent(UserInvited::class);
    }

    public function testStoreUserWithoutEmailShouldHaveErrors()
    {
        $user = factory(User::class)->create();
        $user->markEmailAsVerified();

        $response = $this->actingAs($user)
            ->post('/users', [
                '_token' => csrf_token(),
            ]);

        $response->assertSessionHas('errors');
    }
}
