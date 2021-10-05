<?php


namespace App\Infra\Classes\Common;


use App\Infra\Interfaces\Repositories\Common\PhoneRepositoryInterface;
use App\Infra\Interfaces\Validators\PhoneValidatorInterface;
use App\Infra\Repositories\Common\PhoneRepository;
use libphonenumber\PhoneNumber;
use libphonenumber\PhoneNumberType;
use libphonenumber\PhoneNumberUtil;

class PhoneValidator implements PhoneValidatorInterface
{
    private PhoneNumberUtil $phoneUtil;
    private PhoneNumber $phone;
    private string $number;
    private string $countryIso;
    private int $type;

    public function __construct()
    {
        $this->phoneUtil = PhoneNumberUtil::getInstance();
    }

    private function setNumber(string $number)
    {
        $this->number = $number;
    }

    private function setCountryIso(string $countryIso)
    {
        $this->countryIso = $countryIso;
    }

    private function setType(int $type)
    {
        $this->type = $type;
    }

    public function setPhone(string $number, string $countryIso): PhoneValidatorInterface
    {
        $this->setNumber($number);
        $this->setCountryIso($countryIso);
        $this->phone = $this->phoneUtil->parse($number, $countryIso);
        return $this;
    }

    public function setMobile(string $number, string $countryIso): PhoneValidatorInterface
    {
        $this->setPhone($number, $countryIso);
        $this->setType(PhoneNumberType::MOBILE);
        return $this;
    }

    public function setLandLine(string $number, string $countryIso): PhoneValidatorInterface
    {
        $this->setPhone($number, $countryIso);
        $this->setType(PhoneNumberType::FIXED_LINE);
        return $this;
    }

    public function validForType(): bool
    {
        try {
            return $this->phoneUtil->isPossibleNumberForType($this->phone, $this->type)
                && $this->phoneUtil->isValidNumberForRegion($this->phone, $this->countryIso);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function valid(): bool
    {
        try {
            return $this->phoneUtil->isValidNumberForRegion($this->phone, $this->countryIso);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function get(): PhoneRepositoryInterface
    {
        return new PhoneRepository($this->phone);
    }

    public function validForRegex(string $regex = null): bool
    {
        if ($regex) {
            return preg_match("/{$regex}/", $this->number);
        }
        return false;
    }
}
