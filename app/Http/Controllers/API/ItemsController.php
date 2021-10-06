<?php

namespace App\Http\Controllers\API;

use App\Infra\Classes\BusinessLogic\Items\GetItems;
use App\Infra\Classes\Common\APIJsonResponse;

class ItemsController
{
    public function index(GetItems $getItems)
    {
        if ($getItems->validRequest()) {
            return APIJsonResponse::success($getItems->getItems());
        }
        return APIJsonResponse::error($getItems->getErrors());
    }
}
