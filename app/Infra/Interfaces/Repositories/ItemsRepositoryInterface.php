<?php


namespace App\Infra\Interfaces\Repositories;


use App\Infra\Interfaces\Crud\CrudInterface;
use App\Infra\Interfaces\Crud\UpdateInterface;
use App\Infra\Interfaces\Crud\WhereInInterface;

interface ItemsRepositoryInterface extends CrudInterface, RepositoryInterface, UpdateInterface, WhereInInterface
{
    public function available(int $day = null, string $fromTime = null, $toTime = null): ItemsRepositoryInterface;

    public function withValidPromoCodes(array $selectColumns, array $inPromoCodes): ItemsRepositoryInterface;
}
