<?php


namespace App\Infra\Classes\Common;


class APIJsonResponse
{
    public static function success($data = [], $statusCode = 200)
    {
        return response()->json($data, $statusCode);
    }

    public static function error($errors = [], $statusCode = 400)
    {
        return response()->json(['errors' => $errors], $statusCode);
    }

    public static function statusResponse(bool $status, int $statusCode = null)
    {
        if (!$statusCode) {
            $returnStatusCode = $status ? 200 : 400;
        }
        return response()->json(['status' => $status], $statusCode ?? $returnStatusCode);
    }
}
