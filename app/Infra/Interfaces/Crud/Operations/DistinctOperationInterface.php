<?php


namespace App\Infra\Interfaces\Crud\Operations;


interface DistinctOperationInterface
{
    public function distinct($columns);
}
