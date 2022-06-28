<?php
if (! function_exists('gifttify_price_format')) {
    function gifttify_price_format($price, $decimals = 2, $currency = '$')
    {
        return $price >= 0 ? $currency . number_format($price, $decimals) : "-$currency" . number_format(abs($price), $decimals);
    }
}
