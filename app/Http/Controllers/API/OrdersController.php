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
            $createOrder->calculate();
            if ($createOrder->create()) {
                return APIJsonResponse::success([
                    'total' => $createOrder->getTotal(),
                    'sub_total' => $createOrder->getSubTotal(),
                ]);
            }
            return APIJsonResponse::error(Errors::SERVER_ERROR);
        }
        return APIJsonResponse::error($createOrder->getErrors());
    }
}
