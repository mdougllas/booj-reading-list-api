<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Laravel\Passport\Passport;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ApiAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A unauthenticated user cannot send requests to the api.
     *
     * @return void
     */
    public function test_unauthenticated_user_cannot_send_api_requests()
    {
        $response = $this->get('/api/books');

        $response
            ->assertStatus(302)
            ->assertRedirect('api/unauthenticated');
    }

    /**
     * A unauthenticated user have to get the right json response.
     *
     * @return void
     */
    public function test_unauthenticated_user_gets_right_response()
    {
        $response = $this->get('/api/unauthenticated');

        $response
            ->assertStatus(401)
            ->assertExactJson(['Please authenticate on the app to use this resource.']);
    }

    /**
     * A authenticated user can send requests to the api.
     *
     * @return void
     */
    public function test_authenticated_users_can_send_api_requests()
    {
        Passport::actingAs(
            User::factory()->create()
        );

        $response = $this->get('/api/books');

        $response->assertStatus(200);
    }
}
