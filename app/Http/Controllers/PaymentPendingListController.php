<?php

namespace App\Http\Controllers;
use Illuminate\View\View;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\SalesLedger;
use App\Models\ExpenseLedger;
use App\Models\Transaction;
use Razorpay\Api\Api;

class PaymentPendingListController extends Controller
{
    //

    public function index(Request $request): View
    {
        $test = "Test";
        $api = new Api(config('services.razorpay.key'), config('services.razorpay.secret'));

        $getPurchaseData = SalesLedger::where('payment_status', 'pending')
        ->orderBy('sales_date', 'desc')                    
        ->with('user') // Eager load the user relationship
        ->paginate(50);

        $pendingSum = $getPurchaseData->sum('total_customer_price');
        
        foreach ($getPurchaseData as $ledger) {
            if ($ledger->payment_link_id) {
                $paymentLink = $api->invoice->fetch($ledger->payment_link_id);
                $ledger->payment_link_status = $paymentLink->status;
                $ledger->save();
            }
        }
        
        return view(
            'app.sales_ledgers.pending' , compact('getPurchaseData','pendingSum')
        );
    }
}
