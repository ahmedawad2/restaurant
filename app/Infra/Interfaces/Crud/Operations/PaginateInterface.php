<?php


namespace App\Infra\Interfaces\Crud\Operations;


use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface PaginateInterface
{
    public function paginate(int $count):LengthAwarePaginator;
}
