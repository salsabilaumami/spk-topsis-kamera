<?php

namespace App\Support;

final class Number
{
    public static function decimal(float|int|string|null $value, int $maximumDecimals = 6): string
    {
        if ($value === null || $value === '') {
            return '-';
        }

        $formatted = number_format((float) $value, $maximumDecimals, ',', '.');
        $formatted = rtrim(rtrim($formatted, '0'), ',');

        return $formatted === '-0' ? '0' : $formatted;
    }

    public static function percent(float|int|string|null $value, int $maximumDecimals = 2): string
    {
        if ($value === null || $value === '') {
            return '-';
        }

        return self::decimal((float) $value * 100, $maximumDecimals).'%';
    }

    public static function database(float|int|string $value): string
    {
        return sprintf('%.15F', (float) $value);
    }
}
