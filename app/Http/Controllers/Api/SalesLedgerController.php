<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\Product;
use App\Models\ProductMaster;
use App\Models\SalesLedger;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\SalesLedgerResource;
use App\Http\Resources\SalesLedgerCollection;
use App\Http\Requests\SalesLedgerStoreRequest;
use App\Http\Requests\SalesLedgerUpdateRequest;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Razorpay\Api\Api;
use Illuminate\Support\Facades\Log;

class SalesLedgerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): SalesLedgerCollection
    {
        $this->authorize('view-any', SalesLedger::class);

        $search = $request->get('search', '');
        $status = $request->get('payment_status', '');
        $userId = $request->get('user_id', '');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $perPage = $request->get('per_page', 15);

        $currentMonth = now()->month;
        $currentYear = now()->year;

        $salesLedgers = SalesLedger::with(['user', 'products', 'transactions'])
            ->search($search)
            ->orderBy('sales_date', 'desc')
            ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                return $query->whereBetween('sales_date', [$startDate, $endDate]);
            }, function ($query) use ($currentYear, $currentMonth) {
                return $query->whereYear('sales_date', $currentYear)
                             ->whereMonth('sales_date', $currentMonth);
            })
            ->when($status, function ($query, $status) {
                return $query->where('payment_status', $status);
            })
            ->when($userId, function ($query, $userId) {
                return $query->where('user_id', $userId);
            })
            ->latest()
            ->paginate($perPage);

        return new SalesLedgerCollection($salesLedgers);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SalesLedgerStoreRequest $request): JsonResponse
    {
        $this->authorize('create', SalesLedger::class);

        DB::beginTransaction();
        try {
            $validated = $request->validated();
            
            // Handle new user creation
            if ($request->input('userCheck') === "new") {
                $user = User::create([
                    'name' => $request->input('name'),
                    'email' => $request->input('email'),
                    'phone' => $request->input('phone'),
                    'remarks' => $request->input('remarks'),
                ]);
                $validated['user_id'] = $user->id;
            }

            // Set default values
            $validated['sales_date'] = $validated['sales_date'] ?? now();
            $validated['payment_method'] = $validated['payment_method'] === 'Please select' ? 'cash' : $validated['payment_method'];
            $validated['payment_status'] = $validated['payment_status'] === 'Please select' ? 'pending' : $validated['payment_status'];
            $validated['invoice_number'] = $this->generateInvoiceNumber($validated['sales_date']);

            // Initialize totals
            $validated['total_product_price'] = 0;
            $validated['selling_product_price'] = 0;
            $validated['total_customer_price'] = 0;

            $salesLedger = SalesLedger::create($validated);

            $totalProductPrice = 0;
            $totalSellingPrice = 0;
            $totalCustomerPrice = 0;

            // Process products
            foreach ($request->input('products', []) as $product) {
                if (isset($product['selected']) && $product['selected']) {
                    // Update inventory if product master is specified
                    if (!empty($product['product_master_id']) && $product['product_master_id'] != '0') {
                        $productMaster = ProductMaster::find($product['product_master_id']);
                        $productModel = Product::find($product['product_id']);
                        
                        if ($productMaster && $productModel) {
                            $totalUnits = $productMaster->total_piece;
                            $pUnits = $productModel->units;
                            $updatedUnits = $totalUnits - ($product['quantity'] * $pUnits);
                            
                            if ($updatedUnits < 0) {
                                throw new \Exception("Insufficient stock for product: {$product['product_name']}");
                            }
                            
                            $productMaster->update(['total_piece' => $updatedUnits]);
                        }
                    }

                    // Calculate totals
                    $totalProductPrice += ($product['product_price'] * $product['quantity']);
                    $totalSellingPrice += ($product['selling_price'] * $product['quantity']);
                    $totalCustomerPrice += ($product['customer_price'] * $product['quantity']);

                    // Attach product to sales ledger
                    $salesLedger->products()->attach($product['product_id'], [
                        'product_name' => $product['product_name'],
                        'product_price' => $product['product_price'],
                        'selling_price' => $product['selling_price'],
                        'quantity' => $product['quantity'],
                        'customer_price' => $product['customer_price'],
                        'product_master_id' => $product['product_master_id'] ?? null
                    ]);
                }
            }

            // Update totals
            $salesLedger->update([
                'total_product_price' => $totalProductPrice,
                'selling_product_price' => $totalSellingPrice,
                'total_customer_price' => $totalCustomerPrice
            ]);

            // Only create transaction if payment_status is 'paid'
            if ($salesLedger->payment_status === 'paid') {
                $this->updateSales($salesLedger);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Sales ledger created successfully',
                'data' => new SalesLedgerResource($salesLedger->load(['user', 'products', 'transactions']))
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create sales ledger: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, SalesLedger $salesLedger): SalesLedgerResource
    {
        $this->authorize('view', $salesLedger);

        return new SalesLedgerResource($salesLedger->load(['user', 'products', 'transactions']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(SalesLedgerUpdateRequest $request, SalesLedger $salesLedger): JsonResponse
    {
        $this->authorize('update', $salesLedger);

        DB::beginTransaction();
        try {
            $validated = $request->validated();

            // Handle new user creation
            if ($request->input('userCheck') === "new") {
                $user = User::create([
                    'name' => $request->input('name'),
                    'email' => $request->input('email'),
                    'phone' => $request->input('phone'),
                    'remarks' => $request->input('remarks'),
                ]);
                $validated['user_id'] = $user->id;
            }

            // Restore inventory for existing products
            foreach ($salesLedger->products as $product) {
                if ($product->pivot->product_master_id) {
                    $productMaster = ProductMaster::find($product->pivot->product_master_id);
                    if ($productMaster) {
                        $productModel = Product::find($product->id);
                        $restoredUnits = $product->pivot->quantity * $productModel->units;
                        $productMaster->increment('total_piece', $restoredUnits);
                    }
                }
            }

            // Detach existing products
            $salesLedger->products()->detach();

            // Process new products
            $totalProductPrice = 0;
            $totalSellingPrice = 0;
            $totalCustomerPrice = 0;

            foreach ($request->input('products', []) as $product) {
                if (isset($product['selected']) && $product['selected']) {
                    // Update inventory
                    if (!empty($product['product_master_id']) && $product['product_master_id'] != '0') {
                        $productMaster = ProductMaster::find($product['product_master_id']);
                        $productModel = Product::find($product['product_id']);
                        
                        if ($productMaster && $productModel) {
                            $totalUnits = $productMaster->total_piece;
                            $pUnits = $productModel->units;
                            $updatedUnits = $totalUnits - ($product['quantity'] * $pUnits);
                            
                            if ($updatedUnits < 0) {
                                throw new \Exception("Insufficient stock for product: {$product['product_name']}");
                            }
                            
                            $productMaster->update(['total_piece' => $updatedUnits]);
                        }
                    }

                    // Calculate totals
                    $totalProductPrice += ($product['product_price'] * $product['quantity']);
                    $totalSellingPrice += ($product['selling_price'] * $product['quantity']);
                    $totalCustomerPrice += ($product['customer_price'] * $product['quantity']);

                    // Attach product
                    $salesLedger->products()->attach($product['product_id'], [
                        'product_name' => $product['product_name'],
                        'product_price' => $product['product_price'],
                        'selling_price' => $product['selling_price'],
                        'quantity' => $product['quantity'],
                        'customer_price' => $product['customer_price'],
                        'product_master_id' => $product['product_master_id'] ?? null
                    ]);
                }
            }

            // Update sales ledger
            $validated['total_product_price'] = $totalProductPrice;
            $validated['selling_product_price'] = $totalSellingPrice;
            $validated['total_customer_price'] = $totalCustomerPrice;

            $salesLedger->update($validated);

            // Update transaction
            $this->updateTransaction($salesLedger);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Sales ledger updated successfully',
                'data' => new SalesLedgerResource($salesLedger->load(['user', 'products', 'transactions']))
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update sales ledger: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, SalesLedger $salesLedger): JsonResponse
    {
        $this->authorize('delete', $salesLedger);

        DB::beginTransaction();
        try {
            // Restore inventory
            foreach ($salesLedger->products as $product) {
                if ($product->pivot->product_master_id) {
                    $productMaster = ProductMaster::find($product->pivot->product_master_id);
                    if ($productMaster) {
                        $productModel = Product::find($product->id);
                        $restoredUnits = $product->pivot->quantity * $productModel->units;
                        $productMaster->increment('total_piece', $restoredUnits);
                    }
                }
            }

            // Delete related transactions
            $salesLedger->transactions()->delete();

            // Detach products
            $salesLedger->products()->detach();

            // Delete sales ledger
            $salesLedger->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Sales ledger deleted successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete sales ledger: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update payment status
     */
    public function updatePaymentStatus(Request $request, SalesLedger $salesLedger): JsonResponse
    {
        $this->authorize('update', $salesLedger);

        $request->validate([
            'payment_status' => 'required|in:pending,paid,partial,cancelled'
        ]);

        $salesLedger->update(['payment_status' => $request->payment_status]);

        return response()->json([
            'success' => true,
            'message' => 'Payment status updated successfully',
            'data' => new SalesLedgerResource($salesLedger->load(['user', 'products', 'transactions']))
        ]);
    }

    /**
     * Create Razorpay payment link
     */
    public function createPaymentLink(Request $request, SalesLedger $salesLedger): JsonResponse
    {
        $this->authorize('update', $salesLedger);

        try {
            $api = new Api(config('services.razorpay.key'), config('services.razorpay.secret'));

            $paymentLink = $api->paymentLink->create([
                'amount' => $salesLedger->total_customer_price * 100, // Convert to paise
                'currency' => 'INR',
                'accept_partial' => true,
                'reference_id' => $salesLedger->invoice_number,
                'description' => "Payment for invoice {$salesLedger->invoice_number}",
                'callback_url' => config('app.url') . '/api/payment/callback',
                'callback_method' => 'get'
            ]);

            $salesLedger->update(['payment_link' => $paymentLink['short_url']]);

            return response()->json([
                'success' => true,
                'message' => 'Payment link created successfully',
                'data' => [
                    'payment_link' => $paymentLink['short_url'],
                    'payment_id' => $paymentLink['id']
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create payment link: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get sales summary
     */
    public function summary(Request $request): JsonResponse
    {
        $this->authorize('view-any', SalesLedger::class);

        $startDate = $request->get('start_date', now()->startOfMonth());
        $endDate = $request->get('end_date', now()->endOfMonth());

        $summary = SalesLedger::whereBetween('sales_date', [$startDate, $endDate])
            ->select(
                DB::raw('COUNT(*) as total_orders'),
                DB::raw('SUM(total_customer_price) as total_revenue'),
                DB::raw('SUM(total_product_price) as total_cost'),
                DB::raw('AVG(total_customer_price) as average_order_value')
            )
            ->first();

        $summary->total_profit = $summary->total_revenue - $summary->total_cost;
        $summary->profit_margin = $summary->total_revenue > 0 
            ? round(($summary->total_profit / $summary->total_revenue) * 100, 2) 
            : 0;

        return response()->json([
            'success' => true,
            'data' => $summary
        ]);
    }

    /**
     * Generate invoice number
     */
    private function generateInvoiceNumber($salesDate): string
    {
        $carbonDate = Carbon::parse($salesDate);
        $month = $carbonDate->month;

        $totalInvoices = DB::table('sales_ledgers')
            ->whereMonth('sales_date', $month)
            ->count();

        return sprintf('INV%02d%05d', $month, $totalInvoices + 1);
    }

    /**
     * Create transaction record
     */
    private function createTransaction(SalesLedger $salesLedger): void
    {
        $lastTransaction = Transaction::latest()->first();
        
        $bankBalance = $lastTransaction ? $lastTransaction->bank_balance : 0;
        $cashInHand = $lastTransaction ? $lastTransaction->cash_in_hand : 0;

        if ($salesLedger->payment_status === 'paid') {
            if ($salesLedger->payment_method === 'cash') {
                $cashInHand += $salesLedger->total_customer_price;
            } else {
                $bankBalance += $salesLedger->total_customer_price;
            }
        }

        Transaction::create([
            'bank_balance' => $bankBalance,
            'cash_in_hand' => $cashInHand,
            'sales_ledger_id' => $salesLedger->id,
            'reason' => "Sales: {$salesLedger->invoice_number}"
        ]);
    }

    /**
     * Update transaction record
     */
    private function updateTransaction(SalesLedger $salesLedger): void
    {
        $transaction = $salesLedger->transactions()->first();
        if ($transaction) {
            $lastTransaction = Transaction::where('id', '<', $transaction->id)->latest()->first();
            
            $bankBalance = $lastTransaction ? $lastTransaction->bank_balance : 0;
            $cashInHand = $lastTransaction ? $lastTransaction->cash_in_hand : 0;

            if ($salesLedger->payment_status === 'paid') {
                if ($salesLedger->payment_method === 'cash') {
                    $cashInHand += $salesLedger->total_customer_price;
                } else {
                    $bankBalance += $salesLedger->total_customer_price;
                }
            }

            $transaction->update([
                'bank_balance' => $bankBalance,
                'cash_in_hand' => $cashInHand,
                'reason' => "Sales: {$salesLedger->invoice_number}"
            ]);
        }
    }

    /**
     * List all sales ledgers with payment_status 'pending' and return total pending sum.
     * Minimal version for debugging: no relationships, no resource collection.
     */
    public function pending(Request $request): \Illuminate\Http\JsonResponse
    {
        $pendingLedgers = \App\Models\SalesLedger::with('user')
            ->where('payment_status', 'pending')
            ->orderBy('sales_date', 'desc')
            ->get();
        $totalPending = $pendingLedgers->sum('total_customer_price');

        return response()->json([
            'success' => true,
            'data' => $pendingLedgers,
            'total_pending' => $totalPending
        ]);
    }

    /**
     * Update only the payment_status and payment_method of a sales ledger.
     */
    public function updatePaymentInfo(Request $request, $id): \Illuminate\Http\JsonResponse
    {
        $salesLedger = \App\Models\SalesLedger::findOrFail($id);
        $validated = $request->validate([
            'payment_status' => 'sometimes|required|string',
            'payment_method' => 'sometimes|required|string',
        ]);

        if (empty($validated)) {
            return response()->json([
                'success' => false,
                'message' => 'At least one of payment_status or payment_method must be provided.'
            ], 422);
        }

        $salesLedger->update($validated);

        // Always call updateSales after update, like the web controller
        $transaction = $this->updateSales($salesLedger);

        return response()->json([
            'success' => true,
            'message' => 'Payment info updated successfully.',
            'data' => $salesLedger,
            'transaction' => $transaction
        ]);
    }

    /**
     * Create a transaction for the sales ledger if payment_status is 'paid' and one does not exist.
     */
    private function updateSales($salesLedger)
    {
        \Log::info('updateSales called', [
            'sales_ledger_id' => $salesLedger->id,
            'payment_status' => $salesLedger->payment_status,
            'payment_method' => $salesLedger->payment_method,
        ]);
        if ($salesLedger->payment_status === 'paid') {
            $transactionExists = \App\Models\Transaction::where('sales_ledger_id', $salesLedger->id)->exists();
            if (!$transactionExists) {
                $lastTransaction = \App\Models\Transaction::latest('id')->first();
                $bankBalance = $lastTransaction ? $lastTransaction->bank_balance : 0;
                $cashInHand = $lastTransaction ? $lastTransaction->cash_in_hand : 0;
                $reason = '';
                if ($salesLedger->payment_method === 'cash') {
                    $cashInHand += $salesLedger->total_customer_price;
                    $reason = 'Sales of Rs: ' . number_format($salesLedger->total_customer_price, 2) . ' through Cash';
                } elseif ($salesLedger->payment_method === 'bank') {
                    $bankBalance += $salesLedger->total_customer_price;
                    $reason = 'Sales of Rs: ' . number_format($salesLedger->total_customer_price, 2) . ' through Bank';
                } elseif ($salesLedger->payment_method === 'website') {
                    $twoPercentage = $salesLedger->total_customer_price * 0.02;
                    $gstForTwoPercentage = $twoPercentage * 0.18;
                    $totaltax = $twoPercentage + $gstForTwoPercentage;
                    $bankBalance += ($salesLedger->total_customer_price - $totaltax);
                    $reason = 'Sales of Rs: ' . number_format($salesLedger->total_customer_price, 2) . ' through Website';
                }
                \Log::info('Creating transaction', [
                    'bank_balance' => $bankBalance,
                    'cash_in_hand' => $cashInHand,
                    'reason' => $reason,
                ]);
                $transaction = \App\Models\Transaction::create([
                    'bank_balance' => $bankBalance,
                    'cash_in_hand' => $cashInHand,
                    'sales_ledger_id' => $salesLedger->id,
                    'reason' => $reason
                ]);
                return $transaction;
            } else {
                \Log::info('Transaction already exists for sales_ledger_id', [
                    'sales_ledger_id' => $salesLedger->id,
                ]);
                return \App\Models\Transaction::where('sales_ledger_id', $salesLedger->id)->first();
            }
        }
        return null;
    }
}
