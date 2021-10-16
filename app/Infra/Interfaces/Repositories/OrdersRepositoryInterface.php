<?php


namespace App\Infra\Interfaces\Repositories;


use App\Infra\Interfaces\Crud\CrudInterface;
use App\Infra\Interfaces\Crud\Operations\CountOperationInterface;
use App\Infra\Interfaces\Crud\UpdateInterface;

interface OrdersRepositoryInterface extends CrudInterface, RepositoryInterface, UpdateInterface, CountOperationInterface
{
    //Relations
    public function withReservation(array $selectColumns): OrdersRepositoryInterface;

    public function withItems(array $selectColumns): OrdersRepositoryInterface;

    public function withPromoCodes(array $selectColumns): OrdersRepositoryInterface;
}
