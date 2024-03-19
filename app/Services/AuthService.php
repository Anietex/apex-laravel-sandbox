<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\Contracts\RoleRepository;
use App\Repositories\Contracts\UserRepository;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    public function __construct(protected UserRepository $userRepository,
                                protected RoleRepository $roleRepository)
    {

    }


    /**
     * Register a new user
     *
     * @param array $data
     * @return User
     */

    public function register(array $data): User
    {

        $userData = [
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'role_id' => $this->roleRepository->getRoleId('user'),
        ];

        $user =  $this->userRepository->create($userData);


        $token = $user->createToken('auth_token')->plainTextToken;

        return $user->setAttribute('token', $token);


    }

    /**
     * Login a user
     *
     * @param array $data
     * @return User
     * @throws \Exception
     */

    public function login(array $data): User
    {
        $user = $this->userRepository->findByEmail($data['email']);

        if (!$user || !Hash::check($data['password'], $user->password)) {
            throw new \Exception('Invalid credentials');
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return $user->setAttribute('token', $token);
    }
}
