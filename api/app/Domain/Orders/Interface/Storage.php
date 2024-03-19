<?php

namespace App\Domain\Orders\Interface;

use App\Domain\Orders\Order;

interface Storage
{
    public function save(Order $object, string $context);
}
