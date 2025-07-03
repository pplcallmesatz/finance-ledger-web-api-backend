<?php

if (! function_exists('formatCurrency')) {
    /**
     * Format a number as Indian Rupees (INR).
     *
     * @param  float  $number
     * @param  int  $decimals
     * @return string
     */
    function formatCurrency($number, $decimals = 2)
    {
        return '₹ ' . number_format($number, $decimals);
    }
}
