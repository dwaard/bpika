<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserInviteTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function testUnauthorizedShouldResponse404()
    {
        $response = $this->post('/users');

        $response->assertUnauthorized();
    }

    public function testStoreUserHappyPath()
    {
        // See exceptions in the console
        $this->withoutExceptionHandling();

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
        $this->assertDatabaseHas('users', [
            'name' => $invitee->name,
            'email' => $invitee->email,
            'email_verified_at' => null
        ]);
        // Sending the verification notification mail is not tested.
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
