<?php

namespace App\Repositories\Contracts;

interface RoleRepository
{
    public function getRoleId(string $role): int;

    public function getRoleName(int $id): string;




}
