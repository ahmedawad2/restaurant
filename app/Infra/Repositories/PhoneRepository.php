<?php


namespace App\Infra\Repositories;


use App\Infra\Interfaces\Repositories\PhoneRepositoryInterface;
use libphonenumber\PhoneNumber;

class PhoneRepository implements PhoneRepositoryInterface
{
    private PhoneNumber $phone;

    public function __construct(PhoneNumber $phone)
    {
        $this->phone = $phone;
    }

    public function getNumber(): string
    {
        return $this->phone->getNationalNumber();
    }

    public function getCountryCode(): string
    {
        return $this->phone->getCountryCode();
    }
}
