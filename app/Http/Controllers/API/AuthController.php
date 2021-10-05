<?php

namespace App\Http\Controllers\API;

use App\Infra\Classes\BusinessLogic\Customers\CustomerRequest;
use App\Infra\Classes\Common\APIJsonResponse;
use App\Infra\Classes\Common\Errors;
use App\Infra\Interfaces\Repositories\CustomerRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController
{
    public function register(CustomerRequest $customerRequest, CustomerRepositoryInterface $customerRepository)
    {
        if ($customerRequest->validToRegister()) {
            if ($customerRepository->create($customerRequest->dataForCreate())) {
                return APIJsonResponse::statusResponse(true);
            }
        }
        return APIJsonResponse::error($customerRequest->getErrors());
    }

    public function verifyOTP(CustomerRequest $customerRequest, CustomerRepositoryInterface $customerRepository)
    {
        if ($customerRequest->validOTPVerificationAttempt()) {
            $data = $customerRequest->dataForOTPVerification();
            if ($customerRepository
                ->where('mobile', $data['mobile'])
                ->where('otp', $data['otp'])
                ->update(['status' => true])) {
                return APIJsonResponse::statusResponse(true);
            }
        }
        return APIJsonResponse::error($customerRequest->getErrors());
    }

    public function login(CustomerRequest $customerRequest, CustomerRepositoryInterface $customerRepository)
    {

        if ($customerRequest->validForLogin()) {
            $data = $customerRequest->dataForLogin();
            $customer = $customerRepository
                ->where('mobile', $data['mobile'])
                ->select(['id', 'name', 'password', 'status'])
                ->first();
            if ($customer && !$customer->status) {
                return APIJsonResponse::error([Errors::ACCOUNT_NEEDS_VERIFICATION]);
            }
            if (!$customer || !Hash::check($data['password'], $customer->password)) {
                return APIJsonResponse::error([Errors::CREDENTIALS_DO_NOT_MATCH]);
            }
            $data = [
                'name' => $customer->name,
                'mobile' => $data['mobile'],
                'token' => $customer->createToken('auth-token')->plainTextToken,
            ];
            return APIJsonResponse::success($data);
        }
        return APIJsonResponse::error($customerRequest->getErrors());

    }

    public function logout()
    {
        if (Auth::guard('customers')
            ->user()
            ->tokens()
            ->where('id', Auth::guard('customers')->user()->currentAccessToken()->id)
            ->delete()) {
            return APIJsonResponse::statusResponse(true);
        }
        return APIJsonResponse::statusResponse(false);
    }
}
