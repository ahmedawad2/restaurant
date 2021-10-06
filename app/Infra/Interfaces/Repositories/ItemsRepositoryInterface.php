<?php


namespace App\Infra\Interfaces\Repositories;


use App\Infra\Interfaces\Crud\CrudInterface;
use App\Infra\Interfaces\Crud\UpdateInterface;

interface ItemsRepositoryInterface extends CrudInterface, RepositoryInterface, UpdateInterface
{
    public function available(int $day = null, string $fromTime = null, $toTime = null): ItemsRepositoryInterface;
}
