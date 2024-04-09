<?php

namespace App\Base;

abstract class BaseValidator
{
    private $validationErrors = [];

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

    public function throwIfErrors()
    {
        if ($this->validationErrors) {
            throw new CustomException($this->validationErrors);
        }
    }
}
