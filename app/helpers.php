<?php

use App\Support\Number;

if (!function_exists('decimal_value')) {
    function decimal_value(float|int|string|null $value, int $maximumDecimals = 6): string
    {
        return Number::decimal($value, $maximumDecimals);
    }
}

if (!function_exists('percent_value')) {
    function percent_value(float|int|string|null $value, int $maximumDecimals = 2): string
    {
        return Number::percent($value, $maximumDecimals);
    }
}
