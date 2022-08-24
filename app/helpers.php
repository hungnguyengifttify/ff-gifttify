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

if (! function_exists('display_ga_cell_dashboard')) {
    function display_ga_cell_dashboard($gaNumber, $shopifyNumber)
    {
        $gaNumber = round($gaNumber, 2);
        $shopifyNumber = round($shopifyNumber, 2);

        $style = "";
        if ($gaNumber <= 0) {
            $style = " style='color:#ddd' ";
        } else if ($gaNumber != $shopifyNumber) {
            $style = " style='color: #E47401;font-weight: bold;' ";
        }
        return $style;
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
    function display_row_bg_campaign_status($mo, $spend, $status, $account_status = 'ACTIVE')
    {
        if ($account_status == 'DISABLED') {
            return " style='background-color:#ffc107' ";
        } elseif ( $account_status == 'ACTIVE' && ( (0 < $mo && $mo <= 60 && $status == 'PAUSED') || (round($mo) >= 60 && $status == 'ACTIVE') || ($mo == 0 && $spend > 40 && $status == 'ACTIVE') ) ) {
            return " style='background-color:#f66' class='g-warning' ";
        }
        return "";
    }
}

if (! function_exists('display_row_bg_campaign_cpm')) {
    function display_row_bg_campaign_cpm($cpm)
    {
        if ($cpm <= 0) {
            return " style='color:#ddd' ";
        } else if ( $cpm > 20 ) {
            return " style='background-color:#f66' ";
        }
        return "";
    }
}

if (! function_exists('display_row_bg_campaign_cpc')) {
    function display_row_bg_campaign_cpc($cpc)
    {
        if ($cpc <= 0) {
            return " style='color:#ddd' ";
        } else if ( $cpc > 0.8 ) {
            return " style='background-color:#f66' ";
        }
        return "";
    }
}

if (! function_exists('display_row_bg_account_status')) {
    function display_row_bg_account_status($account_status = 'ACTIVE')
    {
        if ( $account_status != 'ACTIVE' ) {
            return " style='background-color:#f66' ";
        }
        return "";
    }
}

