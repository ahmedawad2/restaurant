<?php


namespace App\Infra\Interfaces\Repositories;


use App\Infra\Interfaces\Crud\CrudInterface;
use App\Infra\Interfaces\Crud\InsertInterface;
use App\Infra\Interfaces\Crud\UpdateInterface;

interface ItemsOrdersPromoCodesRepositoryInterface extends CrudInterface, RepositoryInterface, UpdateInterface, InsertInterface
{

}
