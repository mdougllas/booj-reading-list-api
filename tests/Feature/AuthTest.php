<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Laravel\Passport\ClientRepository;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    protected function applyPersonalAccessClient()
    {
        $clientRepository = new ClientRepository();
        $client = $clientRepository->createPersonalAccessClient(
            null, 'Test Personal Access Client', 'http://localhost' //change this to any different domain in use
        );

        DB::table('oauth_personal_access_clients')->insert([
            'client_id' => $client->id,
            'created_at' => $this->faker->dateTime()
        ]);
    }

    /**
     * A user can register with email and password.
     *
     * @return void
     */
    public function test_user_can_register()
    {
        $this->applyPersonalAccessClient();
        // $this->withoutExceptionHandling();


        $password = $this->faker->password();
        $user = [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'password' => $password,
            'password_confirmation' => $password
        ];

        $response = $this->post('/api/register', $user);

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'user' => [
                    'name',
                    'email',
                    'updated_at',
                    'created_at',
                    'id'
                ],
                'access_token'
            ]);
    }

    /**
     * A user can not login with wrong credentials.
     *
     * @return void
     */
    public function test_user_cannot_login_with_wrong_credentials()
    {
        $this->applyPersonalAccessClient();

        $user = User::factory()->create([
            'password' => bcrypt('123456')
        ]);

    $response = $this->post('/api/login', [
        'email' => $user->email,
        'password' => '654321'
    ]);

        $response
        ->assertStatus(401)
        ->assertExactJson(['message' => 'Invalid Credentials']);
    }

    /**
     * A user can login with right credentials.
     *
     * @return void
     */
    public function test_user_can_login_with_right_credentials()
    {
        $this->applyPersonalAccessClient();

        $user = User::factory()->create([
            'password' => bcrypt('123456')
        ]);

        $response = $this->post('/api/login', [
            'email' => $user->email,
            'password' => '123456'
        ]);

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'user' => [
                    'name',
                    'email',
                    'updated_at',
                    'created_at',
                    'id'
                ],
                'access_token'
            ]);
    }
}
