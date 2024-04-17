<?php

namespace App\Base;

abstract class BaseUseCase
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
        $e = $this->validationErrors;
        $this->validationErrors = [];
        throw new CustomException($e);
    }

    public function throwIfErrors()
    {
        if ($this->validationErrors) {
            $e = $this->validationErrors;
            $this->validationErrors = [];
            throw new CustomException($e);
        }
    }
}
