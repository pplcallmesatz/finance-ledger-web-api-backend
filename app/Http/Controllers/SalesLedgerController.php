<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Product;
use App\Models\ProductMaster;
use Illuminate\View\View;
use App\Models\SalesLedger;
use Razorpay\Api\Api;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\SalesLedgerStoreRequest;
use App\Http\Requests\SalesLedgerUpdateRequest;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SalesLedgerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $this->authorize('view-any', SalesLedger::class);
        $search = (string) $request->get('search', '');
        $status = (string) $request->get('payment_status', '');
        $user = (string) $request->get('user_id', '');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
         // Get the current month and year
    $currentMonth = now()->month;
    $currentYear = now()->year;

        $salesLedgers = SalesLedger::search($search)
            ->orderBy('sales_date', 'desc')   
            ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                return $query->whereBetween('sales_date', [$startDate, $endDate]);
            }, function ($query) use ($currentYear, $currentMonth) {
                // Default to the current month if no date range is specified
                return $query->whereYear('sales_date', $currentYear)
                             ->whereMonth('sales_date', $currentMonth);
            })  
            ->when($status, function ($query, $status) {
                return $query->where('payment_status', $status);
            })
            ->when($user, function ($query, $user) {
                return $query->where('user_id', $user);
            })
            ->latest()
            ->paginate(50)
            ->withQueryString();
            $userList = User::pluck('name', 'id'); 
            $displayTotal=[];
            // Calculate the sums
            $displayTotal['totalProductPrice'] = $salesLedgers->sum('total_product_price');
            $displayTotal['totalCustomerPrice'] = $salesLedgers->sum('total_customer_price');
            $displayTotal['pendingTotalCustomerPrice'] = $salesLedgers
        ->where('payment_status', 'pending')
        ->sum('total_customer_price');
        
        return view(
            'app.sales_ledgers.index',
            compact('salesLedgers', 'search','user','userList','displayTotal')
        );
    }

    /**
     * Show the form for creating a new resource.
     */


    public function create(Request $request): View
    {
        $this->authorize('create', SalesLedger::class);
        
        $users = User::select('id', 'name', 'phone')->get();

        $products = Product::get();

        return view('app.sales_ledgers.create', compact('users', 'products'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SalesLedgerStoreRequest $request): RedirectResponse
    {

        $this->authorize('create', SalesLedger::class);
        
        $validated = $request->validated();
        $validated['total_product_price'] = 0;
        $validated['selling_product_price'] = 0;
        $validated['total_customer_price'] = 0;
        $validated['user_id'] = $request['user_id'];
        // if($request['userCheck'] === "new"){
        //     $validateUser = [];
        //     $validateUser['name'] = $request['name'];
        //     $validateUser['email'] = $request['email'];
        //     $validateUser['phone'] = $request['phone'];
        //     $validateUser['remarks'] = $request['remarks'];
        //     $createuser = User::create($validateUser);
        //     $validated['user_id'] = $createuser->id;
        // }
        if ($request['userCheck'] === "new") {
            $validated['user_id'] = User::create([
                'name' => $request['name'],
                'email' => $request['email'],
                'phone' => $request['phone'],
                'remarks' => $request['remarks'],
            ])->id;
        }
        $validated['sales_date'] = $validated['sales_date'] ?? now();
        $validated['payment_method'] = $validated['payment_method'] === 'Please select' ? 'cash' : $validated['payment_method'];
        $validated['payment_status'] = $validated['payment_status'] === 'Please select' ? 'pending': $validated['payment_status'];
            
            $validated['invoice_number'] = $this -> generateInvoiceNumber($validated['sales_date']);
        

        $salesLedger = SalesLedger::create($validated);
        $totalProductPrice = 0; 
        $totalSellingPrice = 0; 
        $totalCustomerPrice = 0; 

        foreach ($request->products as $product) {
            if (isset($product['selected'])) {
 
                if($product['product_master_id'] != '0'){
                    // Retrieve total_piece from product_master
                    $productMaster = ProductMaster::find($product['product_master_id']);
                    // Retrieve unit from products
                    $productModel = Product::find($product['product_id']);
                    $totalUnits = $productMaster -> total_piece;
                    $pUnits = $productModel->units;
                    // Calculate updated units
                    $updatedUnits = $totalUnits - ($product['quantity'] * $pUnits);     
                    // Update the total_piece column in the product_master table
                    $productMaster->update(['total_piece' => $updatedUnits]);
                }
                
                // Calculate totals
                $totalProductPrice = $totalProductPrice + ($product['product_price'] * $product['quantity']);
                $totalSellingPrice = $totalSellingPrice + ($product['selling_price'] * $product['quantity']);
                $totalCustomerPrice = $totalCustomerPrice + ($product['customer_price'] * $product['quantity']);
                
                // Attach product to sales ledger
                $salesLedger->products()->attach($product['product_id'], [
                    'product_name' => $product['product_name'],
                    'product_price' => $product['product_price'],
                    'selling_price' => $product['selling_price'],
                    'quantity' => $product['quantity'],
                    'customer_price' => $product['customer_price'],
                    'product_master_id'=>$product['product_master_id']
                ]);
            }
        }
        $salesLedger->update(['total_product_price' => $totalProductPrice]);
        $salesLedger->update(['selling_product_price' => $totalSellingPrice]);
        $salesLedger->update(['total_customer_price' => $totalCustomerPrice]);
        $updateSales = $this -> updateSales($salesLedger);   

        return redirect()
            ->route('sales-ledgers.show', $salesLedger)
            ->withSuccess(__('crud.common.created'));
    }
    function generateInvoiceNumber($salesMonth)
    {
        // Parse the date string using Carbon
        $carbonDate = Carbon::parse($salesMonth);
        // Get the month from the Carbon object
        $month = $carbonDate->month;

        // Get the total count of invoices for the given month
        $totalInvoices = DB::table('sales_ledgers')
            ->whereMonth('sales_date', $month)
            ->count();


        // Generate the invoice number based on the total count
        $invoiceNumber = sprintf('INV%02d%05d', $month, $totalInvoices + 1);
    
        return $invoiceNumber;
    }
    /**
     * Display the specified resource.
     */
    public function show(Request $request, SalesLedger $salesLedger): View
    {
        $this->authorize('view', $salesLedger);
        $users = User::pluck('name', 'id');

        $products = Product::get();
        
        // / Load relationships
    $salesLedger->load('products');

    // Get all product_master_ids associated with the sales ledger
    $productMasterIds = $salesLedger->products->pluck('pivot.product_master_id')->unique()->toArray();

    // Fetch product master records
    $productMasters = ProductMaster::whereIn('id', $productMasterIds)->pluck('batch_number', 'id');

        return view('app.sales_ledgers.show', compact('salesLedger', 'products','productMasters' ));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, SalesLedger $salesLedger): View
    {
        $this->authorize('update', $salesLedger);

        $users = User::select('id', 'name', 'phone')->get();

        $products = Product::get();

        // / Load relationships
    $salesLedger->load('products');

    // Get all product_master_ids associated with the sales ledger
    $productMasterIds = $salesLedger->products->pluck('pivot.product_master_id')->unique()->toArray();

    // Fetch product master records
    $productMasters = ProductMaster::whereIn('id', $productMasterIds)->pluck('batch_number', 'id');

        return view(
            'app.sales_ledgers.edit',
            compact('salesLedger', 'users', 'products','productMasters')
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(
        SalesLedgerUpdateRequest $request,
        SalesLedger $salesLedger
    ): RedirectResponse {
        $this->authorize('update', $salesLedger);

        $validated = $request->validated();
        $salesLedger->update($validated);
        $salesLedger->products()->detach();
        $totalProductPrice = 0;
        $totalSellingPrice = 0; 
        $totalCustomerPrice = 0; 
        
        foreach ($request->products as $product) {
            if (isset($product['selected'])) {

                
                
                $totalProductPrice = $totalProductPrice + ($product['product_price'] * $product['quantity']);
                $totalSellingPrice = $totalSellingPrice + ($product['selling_price'] * $product['quantity']);
                $totalCustomerPrice = $totalCustomerPrice + ($product['customer_price'] * $product['quantity']);
                $salesLedger->products()->attach($product['product_id'], [
                    'product_name' => $product['product_name'],
                    'product_price' => $product['product_price'],
                    'selling_price' => $product['selling_price'],
                    'quantity' => $product['quantity'],
                    'customer_price' => $product['customer_price'],
                    'product_master_id' => $product['product_master_id']
                ]);
            }
        }
        
        $salesLedger->update(['total_product_price' => $totalProductPrice]);
        $salesLedger->update(['selling_product_price' => $totalSellingPrice]);
        $salesLedger->update(['total_customer_price' => $totalCustomerPrice]);
        $updateSales = $this -> updateSales($salesLedger);   

        return redirect()
            ->route('sales-ledgers.show', $salesLedger)
            ->withSuccess(__('crud.common.saved'));
    }
    public function productClaculation(){
    
    }
    public function updateSales($salesLedger)
    {
        $transLastRecord = Transaction::query()->latest('id')->first();
        $transLastRecord = $transLastRecord->toArray();
            unset($transLastRecord['created_at']);
            unset($transLastRecord['upated_at']);
            unset($transLastRecord['expense_ledger_id']);
            $ab = $salesLedger['payment_method'];
            $ac = $salesLedger['payment_status'];
            if($salesLedger['payment_status'] === 'paid'){
                if($salesLedger['payment_method'] === 'cash'){
                    $transLastRecord['cash_in_hand'] = $transLastRecord['cash_in_hand'] + $salesLedger['total_customer_price'];
                    $transLastRecord['reason'] = "Sales of Rs: ". formatCurrency($salesLedger['total_customer_price']) ." through Cash";
                }
                elseif($salesLedger['payment_method'] === 'bank'){
                    $transLastRecord['bank_balance'] = $transLastRecord['bank_balance'] + $salesLedger['total_customer_price'];
                    $transLastRecord['reason'] = "Sales of Rs: ". formatCurrency($salesLedger['total_customer_price']) ." through Bank";
                }
                elseif($salesLedger['payment_method'] === 'website'){
                    $twoPercentage = $salesLedger['total_customer_price'] * 0.02;
                    $gstForTwoPercentage = $twoPercentage * 0.18;
                    $totaltax = $twoPercentage + $gstForTwoPercentage;
                    $transLastRecord['bank_balance'] = $transLastRecord['bank_balance'] + ($salesLedger['total_customer_price'] - $totaltax);
                    $transLastRecord['reason'] = "Sales of Rs: ". formatCurrency($salesLedger['total_customer_price']) ." through Website";
                }
                $transLastRecord['sales_ledger_id'] = $salesLedger['id'];
                $exists = Transaction::where('sales_ledger_id', $salesLedger['id'])->exists();
                    if($exists){
                        return response()->json(['message' => 'Record exists.']);
                    }
                    else{
                        // return response()->json(['message' => 'Record does not exist.']);
                        $transaction = Transaction::create($transLastRecord);
                    }
            }
        // if($salesLedger['payment_method'] === 'cash'){
        //  $transLastRecord['cash_in_hand'] = $transLastRecord['cash_in_hand'] - $salesLedger['purchase_price'];
        // }
        // else{
        //  $transLastRecord['bank_balance'] = $transLastRecord['bank_balance'] - $salesLedger['purchase_price'];
        // }
        
        
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(
        Request $request,
        SalesLedger $salesLedger
    ): RedirectResponse {
        $this->authorize('delete', $salesLedger);

        $salesLedger->delete();

        return redirect()
            ->route('sales-ledgers.index')
            ->withSuccess(__('crud.common.removed'));
    }


    public function getProducts($productId, $module)
    {
        $product = Product::find($productId);
        if ($product) {
            if($module =="add"){
            $products = ProductMaster::where('category_id', $product->category_master_id)
                           ->where('total_piece', '>', 0)
                           ->select('id', 'batch_number')
                           ->get();
                           return response()->json($products);
            }
            else{
                $products = ProductMaster::where('category_id', $product->category_master_id)
                ->select('id', 'batch_number')
                ->get();
                return response()->json($products); 
            }
        } else {
            return response()->json(['error' => 'Product not found'], 404);
        }

    }

    public function createPaymentLink(Request $request, $salesLedgerId)
    {
        // Validate request data (if necessary)
        $salesLedger = SalesLedger::findOrFail($salesLedgerId);
    
        $api = new Api(config('services.razorpay.key'), config('services.razorpay.secret'));
    
        // Create order
        $order = $api->order->create([
            'amount' => $salesLedger->total_customer_price, // Assuming amount is stored in paise
            'currency' => 'INR', // Adjust currency as needed
            'receipt' => 'order_rcptid_' . time(),
            'payment_capture' => 1 // Capture the payment immediately
        ]);
        dd($order);
        // Generate payment link
        $checkoutUrl = 'https://checkout.razorpay.com/v1/checkout.js';
    
        // Save payment URL to sales ledger
        // $salesLedger->payment_url = $checkoutUrl; // You might want to store more order details here
        // $salesLedger->save();
    
        // You can return a success response or redirect to a view with the payment URL
        return response()->json(['payment_url' => $checkoutUrl], 200);
    }
}
