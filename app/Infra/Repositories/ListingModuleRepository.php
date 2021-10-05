<?php

namespace App\Infra\Repositories;

use App\Infra\Interfaces\Crud\CrudInterface;
use App\Infra\Interfaces\Repositories\RepositoryInterface;
use App\Infra\Interfaces\Repositories\ListingModuleRepositoryInterface;
use App\Infra\Interfaces\Resources\ResourceInterface;
use App\Infra\Models\Common\Language;
use App\Infra\Models\Common\ListingModule;
use App\Infra\Resources\CollectionResource;
use App\Infra\Traits\CommonRepositoryTrait;

class ListingModuleRepository implements ListingModuleRepositoryInterface
{
    use CommonRepositoryTrait;


    public function create(array $data = [])
    {
        return $this->getCommonRepo()->create($data);
    }

    public function readById($id): ?ResourceInterface
    {
        return $this->getCommonRepo()->readById($id);
    }

    public function readByUnique($key, $value)
    {
        return $this->getCommonRepo()->readByUnique($key, $value);
    }

    public function select(array $columns): ?CrudInterface
    {
        return $this->getCommonRepo()->select($columns);
    }
}
