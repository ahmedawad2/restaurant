<?php


namespace App\Infra\Interfaces\Crud\Operations;


interface OrderByOperationInterface
{
    public function orderBy(string $column, string $direction);
}
