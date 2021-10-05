<?php


namespace App\Infra\Interfaces\Repositories\Common;


use App\Infra\Interfaces\Crud\CrudInterface;
use App\Infra\Interfaces\Crud\UpdateInterface;
use App\Infra\Interfaces\Repositories\RepositoryInterface;

interface CustomerRepositoryInterface extends CrudInterface, RepositoryInterface, UpdateInterface
{

    public function activeCustomers(): CustomerRepositoryInterface;

    public function inActiveCustomers(): CustomerRepositoryInterface;
}
