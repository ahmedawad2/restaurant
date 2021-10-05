<?php


namespace App\Infra\Classes\Common;


class SerialGenerator
{
    const UPPER = 'upper';
    const LOWER = 'lower';
    const UPPER_LOWER = 'both';

    private static array $NUMERS = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9];

    private static array $ALPHA_UPPER = [
        'Q', 'W', 'E', 'R', 'T', 'Y', 'U', 'I', 'O', 'P', 'A', 'S', 'D', 'F', 'G', 'H', 'J',
        'K', 'L', 'Z', 'X', 'C', 'V', 'B', 'N', 'M',
    ];

    private static array $ALPHA_LOWER = [
        'q', 'w', 'e', 'r', 't', 'y', 'u', 'i', 'o', 'p', 'a', 's', 'd', 'f', 'g', 'h', 'j',
        'k', 'l', 'z', 'x', 'c', 'v', 'b', 'n', 'm',
    ];

    private static array $SYMBOLS = [
        '@', '#', '$', '%', '&', '*', '+',
    ];

    private static function generate(int $length, array $chars): string
    {
        $key = '';
        for ($i = 0; $i < $length; $i++) {
            shuffle($chars);
            $key .= $chars[rand(0, count($chars) - 1)];
        }
        return $key;
    }

    private static function getAlphaChars(string $type): array
    {
        switch ($type) {
            case self::UPPER:
                return self::$ALPHA_UPPER;

            case self::LOWER:
                return self::$ALPHA_LOWER;
            default:
                return array_merge(self::$ALPHA_UPPER, self::$ALPHA_LOWER);
        }
    }

    public static function alpha(int $length, string $type = self::UPPER_LOWER): string
    {
        return self::generate($length, self::getAlphaChars($type));
    }

    public static function alphaNum(int $length, string $type = self::UPPER_LOWER): string
    {
        if ($length <= 2) {
            return self::generate($length, array_merge(self::$NUMERS, self::getAlphaChars($type)));
        } else {
            $numbers = self::numeric(floor($length * 40 / 100));
            $alpha = self::alpha($length - strlen($numbers), $type);
            return str_shuffle($numbers . $alpha);
        }
    }

    public static function numeric(int $length): string
    {
        return self::generate($length, self::$NUMERS);
    }

    public static function symbolic(int $length): string
    {
        return self::generate($length, self::$SYMBOLS);
    }

    public static function mix(int $length, $type = self::UPPER_LOWER): string
    {
        if ($length <= 3) {
            return self::generate($length,
                array_merge(self::$NUMERS, self::$SYMBOLS, self::getAlphaChars($type)));
        } else {
            $symbols = self::symbolic(floor($length * 25 / 100));
            $numbers = self::numeric(floor($length * 25 / 100));
            $alpha = self::alpha($length - (strlen($symbols) + strlen($numbers)), $type);
            return str_shuffle($symbols . $numbers . $alpha);
        }
    }
}
