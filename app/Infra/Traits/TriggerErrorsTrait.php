<?php

namespace App\Infra\Traits;

trait TriggerErrorsTrait
{
    private array $errors = [];

    private function trigger($error)
    {
        $this->errors[] = $error;
    }

    private function addErrorToField(string $field, $error)
    {
        $this->errors[$field][] = $error;
    }
}
