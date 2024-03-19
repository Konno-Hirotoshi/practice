<?php

namespace App\Base;

abstract class BaseUseCase
{
    protected $validationErrors = [];

    protected function setError($field, $error = null)
    {
        if ($error === null) {
            $this->validationErrors = $field;
        } else {
            $this->validationErrors[$field] = $error;
        }
        return $this;
    }

    protected function throw()
    {
        throw new CustomException($this->validationErrors);
    }

    protected function throwIfErrors()
    {
        if ($this->validationErrors) {
            throw new CustomException($this->validationErrors);
        }
    }

    protected function context(): string
    {
        return $this::class;
    }
}
