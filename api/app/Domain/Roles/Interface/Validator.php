<?php

namespace App\Domain\Roles\Interface;

use App\Domain\Roles\Role;

interface Validator
{
    public function validate(Role $object);
}
