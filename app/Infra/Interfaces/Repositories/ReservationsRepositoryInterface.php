<?php


namespace App\Infra\Interfaces\Repositories;


use App\Infra\Interfaces\Crud\CrudInterface;
use App\Infra\Interfaces\Crud\UpdateInterface;

interface ReservationsRepositoryInterface extends CrudInterface, RepositoryInterface, UpdateInterface
{

}
