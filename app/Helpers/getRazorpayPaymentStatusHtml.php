<?php
// app/helpers.php

if (!function_exists('getRazorpayPaymentStatusHtml')) {
    function getRazorpayPaymentStatusHtml($status)
    {
        switch ($status) {
            case 'paid':
                return '<span class="rounded-md bg-green-50 mx-2 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20">Paid</span>';
            case 'issued':
                return '<span class="rounded-md bg-sky-50 mx-2 px-2 py-1 text-xs font-medium text-sky-700 ring-1 ring-inset ring-sky-600/20	">Issued</span>';
            default:
                return '<span class="unknown">Unknown</span>';
        }
    }
}
