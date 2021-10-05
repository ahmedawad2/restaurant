<?php

namespace App\Infra\Classes\BusinessLogic\Customers;

use App\Infra\Classes\Common\Errors;
use App\Infra\Classes\Common\SerialGenerator;
use App\Infra\Interfaces\Validators\PhoneValidatorInterface;
use App\Infra\Interfaces\Validators\ValidatorInterface;
use App\Infra\Traits\TriggerErrorsTrait;

class CustomerRequest
{
    use TriggerErrorsTrait;

    private ValidatorInterface $validator;
    private PhoneValidatorInterface $phoneValidator;

    public function __construct(ValidatorInterface $validator, PhoneValidatorInterface $phoneValidator)
    {
        $this->validator = $validator;
        $this->phoneValidator = $phoneValidator;
    }

    private function validMobile(): bool
    {
        $this->phoneValidator->setMobile($this->validator->getResource()->get('mobile'), 'EG');
        $valid = $this->phoneValidator->valid();
        if (!$valid) {
            $this->addErrorToField('mobile', Errors::INVALID_MOBILE_FORMAT);
        }
        return $valid;
    }

    private function uniqueMobile(): bool
    {
        $this->validator->string(['mobile'])->unique('mobile', 'customers', 'mobile');
        return $this->validator->validate();
    }

    private function validName(): bool
    {
        if ($this->validator->sent('name')) {
            $this->validator->string(['name']);
            return $this->validator->validate();
        }
        return true;
    }

    private function validOTP(): bool
    {
        if ($this->validator->sent('otp')) {
            $this->validator->string(['otp'])->max('otp', 5);
            return $this->validator->validate();
        }
        return true;
    }

    private function validPassword(): bool
    {
        $this->validator->string(['password'])->min('password', 6);
        return $this->validator->validate();
    }

    public function validToRegister(): bool
    {
        return $this->validName()
            && $this->validPassword()
            && $this->validMobile()
            && $this->uniqueMobile();
    }

    public function validOTPVerificationAttempt(): bool
    {
        return $this->validOTP() && $this->validMobile();
    }

    public function validForLogin(): bool
    {
        return $this->validPassword() && $this->validMobile();
    }

    public function dataForCreate(): array
    {
        return [
            'mobile' => $this->phoneValidator->get()->getNumber(),
            'password' => $this->validator->getResource()->get('password'),
            'name' => $this->validator->getResource()->get('name'),
            'otp' => SerialGenerator::numeric(5),
        ];
    }

    public function dataForOTPVerification(): array
    {
        return [
            'mobile' => $this->phoneValidator->get()->getNumber(),
            'otp' => $this->validator->getResource()->get('otp'),
        ];
    }

    public function dataForLogin(): array
    {
        return [
            'mobile' => $this->phoneValidator->get()->getNumber(),
            'password' => $this->validator->getResource()->get('password'),
        ];
    }

    public function getErrors(): array
    {
        return array_merge($this->errors, $this->validator->getErrors());
    }
}
