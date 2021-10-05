<?php

namespace App\Infra\Interfaces\Crud;


use App\Infra\Interfaces\Crud\Operations\SelectOperationInterface;
use App\Infra\Interfaces\Repositories\ResetInterface;

interface CrudInterface extends CreateInterface, ReadByIdInterface, WhereInterface, SelectOperationInterface,ResetInterface
{
}
