<?php


namespace App\Infra\Resources;


use App\Infra\Interfaces\Resources\ResourceInterface;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class CollectionResource implements ResourceInterface
{
    private $resource;

    public function __construct(Collection $collection)
    {
        $this->resource = $collection;
    }

    public function get($input): Collection
    {
        if (is_array($input) && !Arr::isAssoc($input)) {
            $data = new Collection();
            foreach ($this->resource as $item) {
                foreach ($input as $key) {
                    $record[$key] = $item->{$key};
                }
                $data->push($record);
            }
            return $data;
        }
        return $this->resource->pluck($input);
    }

    public function getResource()
    {
        return $this->resource;
    }
}
