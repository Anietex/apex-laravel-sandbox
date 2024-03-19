<?php

namespace App\Repositories;

use App\Models\Role;
use App\Repositories\Contracts\RoleRepository;

class EloquentRoleRepository implements Contracts\RoleRepository
{

    public function getRoleId(string $role): int
    {
        return Role::where('name', $role)->value('id');
    }

    public function getRoleName(int $id): string
    {
        return Role::where('id', $id)->value('name');
    }
}
