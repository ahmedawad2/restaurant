<?php


namespace App\Infra\Resources;


use App\Infra\Interfaces\Resources\ResourceInterface;
use App\Infra\Models\Common;
use Illuminate\Support\Arr;

class ModelResource implements ResourceInterface
{
    private $resource;

    public function __construct(Common $model)
    {
        $this->resource = $model;
    }

    public function get($input)
    {
        if (is_array($input) && !Arr::isAssoc($input)) {
            foreach ($input as $key) {
                $data[$key] = $this->resource->{$key};
            }
            return $data;
        }
        return $this->resource->{$input};
    }

    public function getResource()
    {
        return $this->resource;
    }
}
