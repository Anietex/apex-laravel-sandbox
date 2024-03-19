<?php

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Contracts\UserRepository;

class EloquentUserRepository implements Contracts\UserRepository
{

    public function create(array $data): User
    {

        $data['password'] = bcrypt($data['password']);
        return User::create($data);
    }

    public function update(User $user, array $data): User
    {
        $user->update($data);
        return $user->fresh();
    }

    public function delete(User $user): bool
    {
        return $user->delete();
    }

    public function find(int $id): ?User
    {
        return User::find($id);
    }

    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }
}
