<?php

namespace App\Infra\Interfaces\Crud;

use App\Infra\Interfaces\Resources\ResourceInterface;

interface ReadByIdInterface
{
    public function readById(int $id);
}
