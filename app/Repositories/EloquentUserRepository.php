<?php

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Contracts\UserRepository;

class EloquentUserRepository implements Contracts\UserRepository
{

    public function create(array $data): User
    {

        $data['password'] = bcrypt($data['password']);
        $user = User::create($data);

        return $user->fresh();
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

    public function users(): array
    {
        return User::query()->paginate(10)->toArray();
    }
}
