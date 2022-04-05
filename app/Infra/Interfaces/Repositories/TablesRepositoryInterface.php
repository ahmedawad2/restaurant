<?php


namespace App\Infra\Interfaces\Repositories;


use App\Infra\Interfaces\Crud\CrudInterface;
use App\Infra\Interfaces\Crud\UpdateInterface;

interface TablesRepositoryInterface extends CrudInterface, RepositoryInterface, UpdateInterface
{
    public function forActiveReservation(string $from, string $to, int $capacity): ?array;

    public function forWaitingReservation(string $from, string $to, int $capacity): ?array;

    public function forOverWaitingReservation(string $from, string $to, int $capacity): int;
}
