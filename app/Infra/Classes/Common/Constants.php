<?php

namespace App\Infra\Classes\Common;


class Constants
{
    const DEFAULT_DATE_FORMAT = 'Y-m-d';
    const DEFAULT_TIME_FORMAT = 'H:i:s';
    const RESERVATION_DATE_FORMAT = self::DEFAULT_DATE_FORMAT . ' ' . self::DEFAULT_TIME_FORMAT;
    const PER_PAGE = 20;
}
