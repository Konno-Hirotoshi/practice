<?php

namespace App\Domain\Orders\Interface;

use App\Domain\Orders\Order;

interface Validator
{
    public function validate(Order $object);
}
