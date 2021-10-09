<?php

namespace App\Infra\Interfaces\Crud\Operations;

interface DecrementOperationInterface
{
    public function decrement(string $column, $value);
}
