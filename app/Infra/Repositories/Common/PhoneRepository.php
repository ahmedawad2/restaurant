<?php


namespace App\Infra\Repositories\Common;


use App\Infra\Interfaces\Repositories\Common\PhoneRepositoryInterface;
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
