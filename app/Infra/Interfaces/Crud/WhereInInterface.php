<?php


namespace App\Infra\Interfaces\Crud;


interface WhereInInterface
{
    public function whereIn(string $column, array $values);
}
