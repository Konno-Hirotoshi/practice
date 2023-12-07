<?php

namespace App\Base;

abstract class BaseValidator
{
    private $validationErrors = [];

    public function setError($field, $error)
    {
        $this->validationErrors[$field] = $error;
        return $this;
    }

    public function throwIfErrors()
    {
        if ($this->validationErrors) {
            throw new CustomException($this->validationErrors);
        }
    }
}