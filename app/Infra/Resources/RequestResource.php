<?php


namespace App\Infra\Resources;


use App\Infra\Interfaces\Resources\ResourceInterface;
use Illuminate\Http\Request;

class RequestResource implements ResourceInterface
{
    private Request $resource;

    public function __construct(Request $request)
    {
        $this->resource = $request;
    }

    public function get($input)
    {
        $explode = explode('.', $input);
        if (count($explode) > 1) {
            $value = $this->resource->get($explode[0]);
            for ($i = 1; $i < count($explode); $i++) {
                $value = @$value[$explode[$i]];
            }
            return $value;
        } else {
            return $this->resource->get($explode[0]);
        }
    }

    public function all(): ?array
    {
        return $this->resource->all() ?? null;
    }

    public function set(string $key, $value): ResourceInterface
    {
        $this->resource[$key] = $value;
        return $this;
    }
}
