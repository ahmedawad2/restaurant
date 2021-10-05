<?php


namespace App\Infra\Interfaces\Repositories;


interface PhoneRepositoryInterface
{
    public function getNumber(): string;

    public function getCountryCode(): string;
}
