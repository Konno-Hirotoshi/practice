<?php

namespace App\Base;

use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Auth\Authenticatable;
use JsonSerializable;

class User implements AuthenticatableContract, JsonSerializable
{
    use Authenticatable;

    /**
     * Create a new user instance.
     *
     * @param int $id
     * @return void
     */
    public function __construct(
        readonly public int $id
    ) {
    }

    /**
     * Get the name of the unique identifier for the user.
     *
     * @return string
     */
    public function getAuthIdentifierName()
    {
        return 'id';
    }

    /**
     * Get the unique identifier for the user.
     *
     * @return mixed
     */
    public function getAuthIdentifier()
    {
        return $this->id;
    }

    /**
     * Implement JsonSerializable
     */
    public function jsonSerialize(): mixed
    {
        return $this->id;
    }

    /**
     * Magic method __toString
     */
    public function __toString()
    {
        return $this->id;
    }
}
