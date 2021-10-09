<?php

namespace App\Http\Controllers\API;

use App\Infra\Classes\BusinessLogic\Orders\CreateOrder;
use App\Infra\Classes\Common\APIJsonResponse;
use App\Infra\Classes\Common\Errors;

class OrdersController
{
    public function store(CreateOrder $createOrder)
    {
        if ($createOrder->validRequest()) {
            if ($createOrder->create()) {
                return APIJsonResponse::success($createOrder->getOrder());
            }
            return APIJsonResponse::error(Errors::SERVER_ERROR);
        }
        return APIJsonResponse::error($createOrder->getErrors());
    }

    public function checkout()
    {

    }
}
