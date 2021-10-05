<?php

namespace App\Infra\Interfaces\Crud;


interface ReadByUniqueInterface
{
    public function readByUnique($key, $value);
}
