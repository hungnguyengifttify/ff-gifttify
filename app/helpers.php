<?php
if (! function_exists('gifttify_price_format')) {
    function gifttify_price_format($price, $currency = '$')
    {
        return $price >= 0 ? $currency . number_format($price, 2) : "-$currency" . number_format(abs($price), 2);
    }
}
