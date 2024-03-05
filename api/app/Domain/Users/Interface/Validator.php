<?php

namespace App\Domain\Users\Interface;

use App\Domain\Users\User;

interface Validator
{
    public function validate(User $object);
}
