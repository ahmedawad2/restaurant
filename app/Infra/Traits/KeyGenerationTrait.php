<?php

namespace App\Infra\Traits;

use App\Infra\Classes\Common\SerialGenerator;

const TYPE_NUMERIC = 'numeric';
const TYPE_ALPHA_NUMERIC = 'alphanumeric';

trait KeyGenerationTrait
{
    /**
     * define those properties in whichever Model you'll use this trait on:
     * protected static string $keyColumnName;
     * protected static int $keyLength;
     * protected static string $keyGenerationType; -> values are the upper constants
     */

    private static function isKeyUnique(string $key, string $columnName): bool
    {
        return !static::where($columnName, $key)->first();
    }

    private static function validateKeyGenerationType(string $keyGenerationType): bool
    {
        return in_array($keyGenerationType, [
            TYPE_NUMERIC, TYPE_ALPHA_NUMERIC
        ]);
    }

    private static function getKeyGenerationAlgorithm(string $keyGenerationType, int $length): string
    {
        return $keyGenerationType == TYPE_ALPHA_NUMERIC
            ? SerialGenerator::alphaNum($length,)
            : SerialGenerator::numeric($length);
    }

    public static function generateUnique(string $keyGenerationType = null, string $columnName = null, int $length = null): string
    {
        $columnName = $columnName ?? static::$keyColumnName;
        $length = $length ?? static::$keyLength;
        $keyGenerationType = $keyGenerationType ?? TYPE_ALPHA_NUMERIC;

        if (self::validateKeyGenerationType($keyGenerationType)) {
            $key = self::getKeyGenerationAlgorithm($keyGenerationType, $length);
            return self::isKeyUnique($key, $columnName) ?
                $key : self::generateUnique($keyGenerationType, $columnName, $length);
        }
        throw new \Exception('undefined key generation type ' . $keyGenerationType);
    }

    public static function generate(string $keyGenerationType = null, int $length = null): string
    {
        $length = $length ?? static::$keyLength;
        $keyGenerationType = $keyGenerationType ?? TYPE_ALPHA_NUMERIC;
        if (self::validateKeyGenerationType($keyGenerationType)) {
            return self::getKeyGenerationAlgorithm($keyGenerationType, $length);
        }
        throw new \Exception('undefined key generation type ' . $keyGenerationType);
    }
}

