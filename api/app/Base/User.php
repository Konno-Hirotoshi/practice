<?php

namespace App\Base;

use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Auth\Authenticatable;
use JsonSerializable;

readonly class User implements AuthenticatableContract, JsonSerializable
{
    use Authenticatable;

    /**
     * Create a new user instance.
     *
     * @param int $id
     * @param int $departmentId
     */
    public function __construct(
        public int $id,
        public int $departmentId,
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
     * @return int
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
