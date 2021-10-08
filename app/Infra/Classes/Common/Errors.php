<?php

namespace App\Infra\Classes\Common;

class Errors
{
    const INVALID_PROMO_CODE = 'INVALID_PROMO_CODE';
    const DUPLICATED_PROMO_CODE = 'DUPLICATED_PROMO_CODE';
    const INVALID_OTP = 'INVALID_OTP';
    const CREDENTIALS_DO_NOT_MATCH = 'CREDENTIALS_DO_NOT_MATCH';
    const INVALID_MOBILE_FORMAT = 'INVALID_MOBILE_FORMAT';
    const ACCOUNT_NEEDS_VERIFICATION = 'ACCOUNT_NEEDS_VERIFICATION';
    const SERVER_ERROR = 'SERVER_ERROR';
    const NO_TABLE_AVAILABLE = 'NO_TABLE_AVAILABLE';
    const RESERVATION_UNAVAILABLE = 'RESERVATION_UNAVAILABLE';
    const INVALID_OR_OUT_OF_STOCK_ITEM = 'INVALID_OR_OUT_OF_STOCK_ITEM';
}
