<?php

namespace App\Repositories\Contracts;

use App\Models\User;

interface UserRepository
{

    public function getAllUsers(): array;

    public function getUserUsers($userId): array;


    public function create(array $data): User;


    public function update(User $user, array $data): User;


    public function delete(User $user): bool;

    public function find(int $id): ?User;

    public function findByEmail(string $email): ?User;


}
