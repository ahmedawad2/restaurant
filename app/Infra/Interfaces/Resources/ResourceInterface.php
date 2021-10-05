<?php


namespace App\Infra\Interfaces\Resources;

interface ResourceInterface
{
    public function get($input);

    public function all(): ?array;

    public function set(string $key, $value): ResourceInterface;
}
