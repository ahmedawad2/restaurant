<?php

namespace App\Infra\Interfaces\Crud;

interface InsertInterface
{
    public function insert(array $data): bool;
}
