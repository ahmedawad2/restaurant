<?php


namespace App\Infra\Interfaces\Crud\Operations;

interface SelectOperationInterface
{
    public function select(array $columns);
}
