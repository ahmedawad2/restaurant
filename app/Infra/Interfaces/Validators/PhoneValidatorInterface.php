<?php


namespace App\Infra\Interfaces\Validators;


use App\Infra\Interfaces\Repositories\PhoneRepositoryInterface;

interface PhoneValidatorInterface
{
    public function setPhone(string $number, string $countryIso): PhoneValidatorInterface;

    public function setMobile(string $number, string $countryIso): PhoneValidatorInterface;

    public function setLandLine(string $number, string $countryIso): PhoneValidatorInterface;

    public function validForType(): bool;

    public function valid(): bool;

    public function validForRegex(string $regex): bool;

    public function get(): PhoneRepositoryInterface;
}
