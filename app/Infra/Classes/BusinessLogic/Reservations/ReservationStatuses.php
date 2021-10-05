<?php

namespace App\Infra\Classes\BusinessLogic\Reservations;

class ReservationStatuses
{
    const RESERVATION_ACTIVE = 1;
    const RESERVATION_SETTLED = 2;
    const RESERVATION_WAITING = 3;

    public static function allStatuses(): array
    {
        return [
            self::RESERVATION_ACTIVE,
            self::RESERVATION_SETTLED,
            self::RESERVATION_WAITING
        ];
    }
}
