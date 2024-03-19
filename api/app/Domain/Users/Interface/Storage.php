<?php

namespace App\Domain\Users\Interface;

use App\Domain\Users\User;

interface Storage
{
    public function save(User $object, string $context);
}
