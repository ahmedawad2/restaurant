<?php

namespace App\Infra\Traits;

trait TriggerErrorsTrait
{
    private array $errors = [];

    private function trigger($error)
    {
        $this->errors[] = $error;
    }

    private function addErrorToField(string $field, $error, bool $append = true)
    {
        if ($append) {
            $this->errors[$field][] = $error;
        } else {
            $this->errors[$field] = $error;
        }
    }
}
