<?php


namespace App\Infra\Interfaces\Repositories;


use App\Infra\Interfaces\Crud\CrudInterface;
use App\Infra\Interfaces\Crud\UpdateInterface;

interface TablesRepositoryInterface extends CrudInterface, RepositoryInterface, UpdateInterface
{
    public function toBeReserved(string $from, string $to, int $capacity): ?array;
}
