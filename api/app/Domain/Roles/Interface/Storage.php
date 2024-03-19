<?php

namespace App\Domain\Roles\Interface;

use App\Domain\Roles\Role;

interface Storage
{
    public function save(Role $object, string $context);
}
