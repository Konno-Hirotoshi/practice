<?php

namespace App\Base;

use Illuminate\Validation\ValidationException;

class CustomException extends ValidationException
{
    /**
     * @var array $body
     */
    private array $body;

    /**
     * Create a new exception instance.
     *
     * @param array|string $body
     * @return void
     */
    public function __construct(array|string $body)
    {
        $this->body = is_array($body) ? $body : ['reason' => $body];
    }

    /**
     * Get all of the validation error messages.
     *
     * @return array
     */
    public function errors()
    {
        return $this->body;
    }
}
