<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Passport\ClientRepository;
use Laravel\Passport\Passport;
use Tests\TestCase;
use Throwable;

class AuthTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        $clientRepository = new ClientRepository();
        $clientRepository->createPersonalAccessClient(
            null, 'Test Personal Access Client', 'http://localhost'
        );

    }


    /** @test
     * @throws Throwable
     */
    public function successful_account_creation_with_valid_data()
    {
        $response = $this->postJson('/api/auth/register', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'status',
            'message',
            'data' => [
                'user' => [
                    'id',
                    'name',
                    'email',
                ],
                'token'
            ]
        ]);

        $responseData = $response->decodeResponseJson();
        $this->assertNotEmpty($responseData['data']['token']);
        $this->assertDatabaseHas('users', ['email' => 'john@example.com']);
    }



    /** @test */
    public function failure_account_creation_with_existent_email()
    {
        $existingUser = \App\Models\User::create([
            'name' => 'Jane Doe',
            'role_id' => 2, // 'user'
            'email' => 'jane@example.com',
            'password' => bcrypt('password'),
        ]);

        $response = $this->postJson('/api/auth/register', [
            'name' => 'John Doe',
            'email' => 'jane@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertStatus(422); // 422 Unprocessable Entity
        $response->assertJsonValidationErrors(['email']);
    }


    /** @test */
    public function failure_account_creation_with_invalid_data()
    {
        $response = $this->postJson('/api/auth/register', [
            'name' => '',
            'email' => 'not-an-email',
            'password' => 'pwd',
            'password_confirmation' => 'pwd',
        ]);

        $response->assertStatus(422); // 422 Unprocessable Entity
        $response->assertJsonValidationErrors(['name', 'email', 'password']);
    }



    /** @test */
    public function successful_login_returns_a_token()
    {

        $user = User::factory()->create([
            'email' => 'user@example.com',
            'name' => 'Test User',
            'role_id' => 2, // 'user'
            'password' => bcrypt('password123'),
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'user@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'status',
            'message',
            'data' => [
                'token' // Ensure the token is present
            ]
        ]);

        // Additionally, check if the token is not empty
        $responseData = $response->decodeResponseJson();
        $this->assertNotEmpty($responseData['data']['token']);
    }



    /** @test */
    public function login_with_incorrect_credentials_fails()
    {
        // Create a user
        User::factory()->create([
            'email' => 'user@example.com',
            'name' => 'Test User',
            'role_id' => 2, // 'user'
            'password' => bcrypt('password123'),
        ]);

        // Attempt to log in with incorrect password
        $response = $this->postJson('/api/auth/login', [
            'email' => 'user@example.com',
            'password' => 'wrongPassword',
        ]);

        $response->assertStatus(401);
        $response->assertJson([
            'status' => 'error',
            'message' => 'Invalid credentials',
        ]);
    }


    /** @test */
    public function user_can_logout_successfully()
    {
        // Create a user
        $user = User::factory()->create([
            'email' => 'user@example.com',
            'name' => 'Test User',
            'role_id' => 2, // 'user'
            'password' => bcrypt('password123'),
        ]);

        $token = $user->createToken('TestToken')->accessToken;

        $response = $this->postJson('/api/auth/logout', [], ['Authorization' => "Bearer $token"]);


        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
            'message' => 'Successfully logged out'
        ]);

    }

}

