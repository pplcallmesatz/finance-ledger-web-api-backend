<?php
// app/Http/Controllers/PaymentController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Razorpay\Api\Api;
use App\Models\SalesLedger;

class PaymentController extends Controller
{
    public function createPaymentLink(Request $request)
    {
        
        $salesLedger = SalesLedger::find($request->sales_ledger_id);
        // Check if a payment link already exists
        if ($salesLedger->payment_link) {
            return response()->json(['message' => 'Payment link already exists', 'payment_link' => $salesLedger->payment_link]);
        }
        $totalProductPrice = $salesLedger -> selling_product_price;
        $productNames = $salesLedger->products->pluck('pivot.product_name')->toArray();
        $productNamesString = implode(', ', $productNames);
        $description = "Purchased Products: " . $productNamesString;

        $api = new Api(config('services.razorpay.key'), config('services.razorpay.secret'));
        $orderData = [
            'type' => 'link',
            'amount' => $totalProductPrice*100, // Amount in paise
            'currency' => 'INR',
            'description' => $description,
            // 'callback_url' => route('payment.callback'),
            'callback_method' => 'get'
        ];

        $paymentLink = $api->invoice->create($orderData);

        
        // Save the payment link in the database
        
        $salesLedger->payment_link = $paymentLink['short_url'];
        $salesLedger->payment_link_id = $paymentLink['id'];
        $salesLedger->payment_link_status= 'created';
        $salesLedger->save();

        return response()->json([
            'payment_link' => $paymentLink['short_url'],
            'sales_ledger_id' => $request->sales_ledger_id
        ]);
    }
}
