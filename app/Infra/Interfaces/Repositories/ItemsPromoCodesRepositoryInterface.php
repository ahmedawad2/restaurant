<?php


namespace App\Infra\Interfaces\Repositories;


use App\Infra\Interfaces\Crud\CrudInterface;
use App\Infra\Interfaces\Crud\UpdateInterface;
use App\Infra\Interfaces\Crud\WhereInInterface;

interface ItemsPromoCodesRepositoryInterface extends CrudInterface, RepositoryInterface, UpdateInterface, WhereInInterface
{

}
