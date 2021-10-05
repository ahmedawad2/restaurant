<?php


namespace App\Infra\Interfaces\Repositories\Common;


interface PhoneRepositoryInterface
{
    public function getNumber(): string;

    public function getCountryCode(): string;
}
