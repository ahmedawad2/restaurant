<?php

namespace App\Http\Controllers\API;

use App\Infra\Classes\BusinessLogic\Reservations\ReservationStatuses;
use App\Infra\Classes\Common\APIJsonResponse;
use App\Infra\Classes\Common\Constants;
use App\Infra\Classes\Common\Errors;
use App\Infra\Interfaces\Repositories\ReservationsRepositoryInterface;
use App\Infra\Interfaces\Repositories\TablesRepositoryInterface;
use App\Infra\Interfaces\Validators\ValidatorInterface;
use Illuminate\Support\Facades\Auth;

class ReservationsController
{
    public function make(ValidatorInterface $validator, TablesRepositoryInterface $tablesRepository
        , ReservationsRepositoryInterface   $reservationsRepository)
    {
        $validator->integer(['capacity'])->min('capacity', 1);
        $validator->matchDateFormat('from', Constants::RESERVATION_DATE_FORMAT);
        $validator->matchDateFormat('to', Constants::RESERVATION_DATE_FORMAT);
        if ($validator->validate()) {
            $table = $tablesRepository->toBeReserved(
                $validator->getResource()->get('from'),
                $validator->getResource()->get('to'),
                $validator->getResource()->get('capacity')
            );
            if ($table) {
                if ($reservation = $reservationsRepository->create([
                    'customer_id' => Auth::guard('customers')->id(),
                    'table_id' => $table['id'],
                    'from' => $validator->getResource()->get('from'),
                    'to' => $validator->getResource()->get('to'),
                    'status' => ReservationStatuses::RESERVATION_ACTIVE
                ])) {
                    return APIJsonResponse::success($reservation);
                }
                return APIJsonResponse::error(Errors::SERVER_ERROR);
            }
            return APIJsonResponse::error(Errors::NO_TABLE_AVAILABLE);
        }
        return APIJsonResponse::error($validator->getErrors());
    }
}
