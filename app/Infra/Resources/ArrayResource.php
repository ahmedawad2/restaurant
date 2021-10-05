<?php


namespace App\Infra\Resources;


use App\Infra\Interfaces\Resources\ResourceInterface;
use Illuminate\Support\Arr;

class ArrayResource implements ResourceInterface
{
    private array $resource = [];

    public function __construct(array $resource)
    {
        if (Arr::isAssoc($resource)) {
            $this->resource = $resource;
        }
        return $this;
    }

    public function get($input)
    {
        $explode = explode('.', $input);
        if (count($explode) > 1) {
            $value = $this->resource[$explode[0]];
            for ($i = 1; $i < count($explode); $i++) {
                $value = @$value[$explode[$i]];
            }
            return $value;
        } else {
            $value = @$this->resource[$explode[0]];
        }
        return $value;
    }

    public function all(): ?array
    {
        return $this->resource;
    }

    public function set(string $key, $value): ResourceInterface
    {
        $this->resource[$key] = $value;
        return $this;
    }
}
