<?php


namespace App\Infra\Interfaces\Crud;


interface WhereInterface
{
    public function where($key, $value, $operator);
}
