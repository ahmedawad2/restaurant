<?php

namespace App\Http\Controllers\API;

use App\Infra\Classes\BusinessLogic\Reservations\MakeReservation;
use App\Infra\Classes\Common\APIJsonResponse;
use App\Infra\Classes\Common\Errors;

class ReservationsController
{
    public function store(MakeReservation $makeReservation)
    {
        if ($makeReservation->validate()) {
            if ($reservation = $makeReservation->make()) {
                return APIJsonResponse::success($reservation);
            }
            return APIJsonResponse::error(Errors::SERVER_ERROR);
        }
        return APIJsonResponse::error($makeReservation->getErrors());
    }
}
