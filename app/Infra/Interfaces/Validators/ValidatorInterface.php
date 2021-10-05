<?php


namespace App\Infra\Interfaces\Validators;


use App\Infra\Classes\Common\Constants;
use App\Infra\Interfaces\Resources\ResourceInterface;

interface ValidatorInterface
{
    public function required(array $inputs): ValidatorInterface;

    public function in($input, array $options): ValidatorInterface;

    public function max($input, int $max): ValidatorInterface;

    public function min($input, int $min): ValidatorInterface;

    public function unique($input, $table, $column, int $ignoreId): ValidatorInterface;

    public function matchDateFormat($input, string $format = Constants::DEFAULT_DATE_FORMAT): ValidatorInterface;

    public function has(array $inputs): bool;

    public function sent(string $input): bool;

    public function integer(array $inputs): ValidatorInterface;

    public function double(array $inputs): ValidatorInterface;

    public function numeric(array $inputs): ValidatorInterface;

    public function string(array $inputs): ValidatorInterface;

    public function boolean(array $inputs): ValidatorInterface;

    public function isArray(array $inputs): ValidatorInterface;

//    public function isNoEmptyArray(array $inputs): ValidatorInterface;

    public function keysInArray(array $keys, $input): ValidatorInterface;

    public function atLeastOneInArray(array $keys, $input): ValidatorInterface;

    public function email(array $inputs): ValidatorInterface;

    public function validate(): bool;

    public function getErrors(): array;

    public function getResource(): ResourceInterface;
}
