<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\Contracts\RoleRepository;
use App\Repositories\Contracts\UserRepository;

class UserService
{



    public function __construct(private readonly UserRepository $userRepository, private readonly RoleRepository $roleRepository)
    {

    }

    public function getUsers(): array
    {
        $user = auth()->user();
        if ($user->role->slug === 'admin') {
            return $this->userRepository->getAllUsers();
        }
        return $this->userRepository->getUserUsers($user->id);
    }

    public function create(array $data): User
    {
        $roleId = $this->roleRepository->getRoleId('user');
        $data['creator_id'] = auth()->id();
        $data['role_id'] = $roleId;

        return $this->userRepository->create($data);
    }

    public function update(User $user, array $data): User
    {
        return $this->userRepository->update($user, $data);
    }

    public function delete(User $user): bool
    {
        return $this->userRepository->delete($user);
    }

    public function find(int $id): ?User
    {
        return $this->userRepository->find($id);
    }

    public function findByEmail(string $email): ?User
    {
        return $this->userRepository->findByEmail($email);
    }




}
