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
     * @return array
     */

    public function register(array $data): array
    {

        $userData = [
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'role_id' => $this->roleRepository->getRoleId('user'),
        ];

        $user =  $this->userRepository->create($userData);
        $token = $user->createToken('auth_token')->accessToken;

        return [
            'user' => $user,
            'token' => $token
        ];

    }

    /**
     * Login a user
     *
     * @param array $data
     * @return array
     * @throws \Exception
     */

    public function login(array $data): array
    {
        $user = $this->userRepository->findByEmail($data['email']);

        if (!$user || !Hash::check($data['password'], $user->password)) {
            throw new \Exception('Invalid credentials');
        }

        $token = $user->createToken('auth_token')->accessToken;

        return [
            'user' => $user,
            'token' => $token
        ];

    }
}
