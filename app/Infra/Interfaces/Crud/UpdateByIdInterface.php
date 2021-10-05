<?php

namespace App\Infra\Interfaces\Crud;

use App\Infra\Interfaces\Repositories\Common\RepositoryInterface;

interface UpdateByIdInterface
{
    public function updateById(int $id, array $data = []);
}
