<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Laravel\Passport\ClientRepository;
use Laravel\Passport\Passport;
use Tests\TestCase;

class UserTest extends TestCase
{

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        $clientRepository = new ClientRepository();
        $clientRepository->createPersonalAccessClient(
            null, 'Test Personal Access Client', 'http://localhost'
        );

    }


    /** @test */
    public function non_authenticated_users_cannot_create_users()
    {
        $userData = [
            'name' => 'John Doe',
            'email' => 'johndoe@example.com',
            'password' => 'password123',
        ];

        $response = $this->postJson('/api/users', $userData);

        $response->assertStatus(401);
    }



    /** @test */
    public function non_authenticated_users_cannot_update_users()
    {
        $user = User::factory()->create();

        $updateData = [
            'name' => 'Updated Name',
            'email' => 'updatedemail@example.com',
        ];

        $response = $this->patchJson("/api/users/{$user->id}", $updateData);


        $response->assertStatus(401);
    }



    /** @test */
    public function non_authenticated_users_cannot_view_users()
    {
        $user = User::factory()->create();

        $response = $this->getJson("/api/users/{$user->id}");

        $response->assertStatus(401);
    }





    /** @test */
    public function authenticated_user_should_be_able_to_create_user()
    {

        $this->authenticateUser();

        $userData = [
            'name' => 'New User',
            'email' => 'newuser@test.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ];

        $response = $this->postJson('/api/users', $userData);

        $response->assertStatus(201);
        $this->assertDatabaseHas('users', ['email' => 'newuser@test.com']);
    }

    /** @test */
    public function authenticated_user_should_be_able_to_update_user_they_created()
    {

        $authUser = $this->authenticateUser();

        $createdUser = User::factory()->create([
            'creator_id' => $authUser->id,
        ]);

        $updateData = [
            'name' => 'Updated Name',
            'email' => 'updatedemail@test.com',
        ];

        $response = $this->patchJson("/api/users/{$createdUser->id}", $updateData);



        $response->assertStatus(200);
        $this->assertDatabaseHas('users', [
            'id' => $createdUser->id,
            'name' => 'Updated Name',
            'email' => 'updatedemail@test.com',
        ]);
    }



    /** @test */
    public function authenticated_users_can_view_only_users_they_created()
    {

        $creatorUser = $this->authenticateUser();


        $createdUsers = User::factory()->count(2)->create([
            'role_id' => $this->getRoleId('user'),
            'creator_id' => $creatorUser->id
        ]);

        // Create a user not created by the authenticated user
        $otherUser = User::factory()->create();


        $response = $this->getJson('/api/users');
        $response->assertStatus(200);


        $userList = $response->json('data.data');
        $this->assertCount(2, $userList);


        foreach ($userList as $user) {
            $this->assertEquals($creatorUser->id, $user['creator_id']);
        }


        $responseForOwnedUser = $this->getJson("/api/users/{$createdUsers->first()->id}");
        $responseForOwnedUser->assertStatus(200);

        $responseForOtherUser = $this->getJson("/api/users/{$otherUser->id}");
        $responseForOtherUser->assertStatus(403);
    }




    public function authenticated_user_should_be_able_to_view_user_they_created()
    {
        $authUser = $this->authenticateUser();

        $createdUser = User::factory()->create([
            'creator_id' => $authUser->id,
        ]);

        $response = $this->getJson("/api/users/{$createdUser->id}");

        $response->assertStatus(200);
        $response->assertJson([
            'id' => $createdUser->id,
            'name' => $createdUser->name,
            'email' => $createdUser->email,
        ]);
    }



    /** @test */
    public function creating_a_user_with_invalid_data_returns_validation_errors()
    {
        $this->authenticateUser();

        $invalidUserData = [
            'name' => '', // Invalid because it's required and cannot be empty
            'email' => 'not-an-email', // Invalid email format
            'password' => 'short', // Assuming the password must be at least 6 characters
        ];

        $response = $this->postJson('/api/users', $invalidUserData);

        $response->assertStatus(422); // HTTP 422 Unprocessable Entity
        $response->assertJsonValidationErrors(['name', 'email', 'password']);
    }

    /** @test */
    public function creating_a_user_with_an_existing_email_returns_a_validation_error()
    {
        $this->authenticateUser();

        // Create a user to simulate an existing email in the database
        $existingUser = User::factory()->create(['email' => 'existinguser@example.com',
        ]);

        $newUserData = [
            'name' => 'John Doe',
            'email' => 'existinguser@example.com', // This email already exists
            'password' => 'password123',
        ];

        $response = $this->postJson('/api/users', $newUserData);

        $response->assertStatus(422); // HTTP 422 Unprocessable Entity
        $response->assertJsonValidationErrors(['email']);
    }

    /** @test */
    public function admin_can_view_all_users_created_by_all_users()
    {
        $adminUser = $this->authenticateUser('admin');



        User::factory()->count(5)->create();


        User::factory()->count(3)->create([
            'creator_id' => $adminUser->id,
        ]);



        $response = $this->getJson('/api/users');


        $response->assertStatus(200);
        $allUsersCount = User::count();
        $response->assertJsonCount($allUsersCount, 'data.data');
    }


    /** @test */
    public function admin_can_update_users_created_by_all_users()
    {
        $adminUser = $this->authenticateUser('admin');

        $userToUpdate = User::factory()->create();

        $newDetails = ['name' => 'Updated Name'];

        $response = $this->patchJson("/api/users/{$userToUpdate->id}", $newDetails);
        $response->assertStatus(200);
        $this->assertDatabaseHas('users', ['id' => $userToUpdate->id, 'name' => 'Updated Name']);
    }


    /** @test */
    public function only_admin_can_delete_users()
    {
        $admin = $this->authenticateUser('admin');

        $userToDelete = User::factory()->create();

        $response = $this->deleteJson("/api/users/{$userToDelete->id}");
        $response->assertStatus(200);

        $nonAdmin = User::factory()->create();
        Passport::actingAs($nonAdmin);

        $anotherUser = User::factory()->create();
        $response = $this->deleteJson("/api/users/{$anotherUser->id}");
        $response->assertStatus(403);
    }



    /** @test */
    public function admin_cannot_delete_itself()
    {
        $admin = $this->authenticateUser('admin');

        $response = $this->deleteJson("/api/users/{$admin->id}");
        $response->assertStatus(403);
    }




    private function authenticateUser($role = 'user')
    {
        $authUser = User::factory()->create([
            'name' => 'Jane Doe',
            'role_id' => $this->getRoleId($role),
            'email' => 'jane@example.com',
            'password' => bcrypt('password'),

        ]);
        Passport::actingAs($authUser);
        return $authUser;
    }


    private function getRoleId(string $roleName): int
    {
        return Role::where('slug', $roleName)->first()->id;
    }











}
