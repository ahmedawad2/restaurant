<?php

namespace App\Infra\Classes\Common;

class Errors
{
    const INVALID_REQUEST_SIGNATURE = 'INVALID_REQUEST_SIGNATURE';
    const INVALID_PROMO_CODE = 'INVALID_PROMO_CODE';
    const ORDER_TOTAL_MISMATCH = 'ORDER_TOTAL_MISMATCH';
    const SMS_GATEWAY_DOWN = 'SMS_GATEWAY_DOWN';
    const MOBILE_ALREADY_EXITS = 'MOBILE_ALREADY_EXITS';
    const INVALID_OTP = 'INVALID_OTP';
    const OLD_PASSWORD_IS_INCORRECT = 'OLD_PASSWORD_IS_INCORRECT';
    const CREDENTIALS_DO_NOT_MATCH = 'CREDENTIALS_DO_NOT_MATCH';
    const SERVER_ISSUE = 'SERVER_ISSUE';
    const SMS_GATEWAY_DOWN_STATUS_CODE = 'SMS_GATEWAY_DOWN_STATUS_CODE0';
    const INVALID_DAY_FOR_REPEATED_PICKUP = 'INVALID_DAY_FOR_REPEATED_PICKUP';
    const INVALID_MOBILE_FORMAT = 'INVALID_MOBILE_FORMAT';
    const INVALID_EMAIL = 'INVALID_EMAIL';
    const NEW_PASSWORD_SAME_OLD_PASSWORD = 'NEW_PASSWORD_SAME_OLD_PASSWORD';
    const ACCOUNT_IS_DISABLE = 'ACCOUNT_IS_DISABLE';
    const ACCOUNT_NEEDS_VERIFICATION = 'ACCOUNT_NEEDS_VERIFICATION';
}