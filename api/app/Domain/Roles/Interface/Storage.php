<?php

namespace App\Domain\Roles\Interface;

use App\Domain\Roles\Role;

interface Storage
{
    public function save(string $type, Role $object);
}
