<?php


namespace App\Infra\Interfaces\Repositories;


use App\Infra\Interfaces\Crud\CrudInterface;
use App\Infra\Interfaces\Crud\UpdateInterface;

interface CustomerRepositoryInterface extends CrudInterface, RepositoryInterface, UpdateInterface
{
    public function activeCustomers(): CustomerRepositoryInterface;

    public function inActiveCustomers(): CustomerRepositoryInterface;
}
