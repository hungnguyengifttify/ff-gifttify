<?php
if (! function_exists('gifttify_price_format')) {
    function gifttify_price_format($price, $decimals = 2, $currency = '$')
    {
        return $price >= 0 ? $currency . number_format($price, $decimals) : "-$currency" . number_format(abs($price), $decimals);
    }
}

if (! function_exists('display_zero_cell_dashboard')) {
    function display_zero_cell_dashboard($number)
    {
        return ($number > 0) ? '' : " style='color:#ddd' ";
    }
}

if (! function_exists('display_row_bg_dashboard')) {
    function display_row_bg_dashboard($number)
    {
        if ($number == 0) {
            return "";
        } elseif (0 < $number && $number < 40) {
            return " style='background-color:#ddffdd' ";
        } elseif (40 <= $number && $number < 50) {
            return " style='background-color:#e0f0ff' ";
        } elseif (50 <= $number && $number < 60) {
            return " style='background-color:#dcdfa8' ";
        } elseif (60 <= $number && $number < 80) {
            return " style='background-color:#ffecd2' ";
        }  else {
            return " style='background-color:#ffcece' ";
        }
        return "";
    }
}

if (! function_exists('display_row_bg_campaign_status')) {
    function display_row_bg_campaign_status($mo, $spend, $status)
    {
        if ( (0 < $mo && $mo < 40 && $status == 'PAUSED') || ($mo > 60) || ($mo == 0 && $spend > 40) ) {
            return " style='background-color:#f66' ";
        }
        return "";
    }
}


