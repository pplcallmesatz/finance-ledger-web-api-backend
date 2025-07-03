<?php

namespace App\Http\Controllers\Api;

use App\Models\SalesLedger;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Razorpay\Api\Api;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    protected $razorpay;

    public function __construct()
    {
        $this->razorpay = new Api(config('services.razorpay.key'), config('services.razorpay.secret'));
    }

    /**
     * Create payment order
     */
    public function createOrder(Request $request): JsonResponse
    {
        $request->validate([
            'sales_ledger_id' => 'required|exists:sales_ledgers,id',
            'amount' => 'required|numeric|min:1',
            'currency' => 'required|string|size:3',
        ]);

        try {
            $salesLedger = SalesLedger::findOrFail($request->sales_ledger_id);

            $orderData = [
                'receipt' => $salesLedger->invoice_number,
                'amount' => $request->amount * 100, // Convert to paise
                'currency' => $request->currency,
                'notes' => [
                    'sales_ledger_id' => $salesLedger->id,
                    'invoice_number' => $salesLedger->invoice_number,
                ]
            ];

            $order = $this->razorpay->order->create($orderData);

            return response()->json([
                'success' => true,
                'message' => 'Payment order created successfully',
                'data' => [
                    'order_id' => $order['id'],
                    'amount' => $order['amount'],
                    'currency' => $order['currency'],
                    'receipt' => $order['receipt'],
                    'status' => $order['status'],
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Payment order creation failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create payment order: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verify payment signature
     */
    public function verifyPayment(Request $request): JsonResponse
    {
        $request->validate([
            'razorpay_order_id' => 'required|string',
            'razorpay_payment_id' => 'required|string',
            'razorpay_signature' => 'required|string',
        ]);

        try {
            $attributes = [
                'razorpay_order_id' => $request->razorpay_order_id,
                'razorpay_payment_id' => $request->razorpay_payment_id,
                'razorpay_signature' => $request->razorpay_signature,
            ];

            $this->razorpay->utility->verifyPaymentSignature($attributes);

            // Get payment details
            $payment = $this->razorpay->payment->fetch($request->razorpay_payment_id);
            $order = $this->razorpay->order->fetch($request->razorpay_order_id);

            // Find sales ledger by receipt
            $salesLedger = SalesLedger::where('invoice_number', $order['receipt'])->first();

            if ($salesLedger) {
                DB::beginTransaction();
                try {
                    // Update payment status
                    $salesLedger->update([
                        'payment_status' => 'paid',
                        'payment_method' => $payment['method'] ?? 'online'
                    ]);

                    // Create transaction record
                    $this->createPaymentTransaction($salesLedger, $payment);

                    DB::commit();

                    return response()->json([
                        'success' => true,
                        'message' => 'Payment verified and processed successfully',
                        'data' => [
                            'payment_id' => $payment['id'],
                            'order_id' => $order['id'],
                            'amount' => $payment['amount'] / 100,
                            'currency' => $payment['currency'],
                            'method' => $payment['method'],
                            'status' => $payment['status'],
                            'sales_ledger' => [
                                'id' => $salesLedger->id,
                                'invoice_number' => $salesLedger->invoice_number,
                                'payment_status' => $salesLedger->payment_status,
                            ]
                        ]
                    ]);

                } catch (\Exception $e) {
                    DB::rollBack();
                    throw $e;
                }
            }

            return response()->json([
                'success' => false,
                'message' => 'Sales ledger not found for this payment'
            ], 404);

        } catch (\Exception $e) {
            Log::error('Payment verification failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Payment verification failed: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Handle Razorpay webhook
     */
    public function webhook(Request $request): JsonResponse
    {
        try {
            $webhookSecret = config('services.razorpay.webhook_secret');
            $webhookSignature = $request->header('X-Razorpay-Signature');

            $this->razorpay->utility->verifyWebhookSignature(
                $request->getContent(),
                $webhookSignature,
                $webhookSecret
            );

            $payload = $request->all();
            $event = $payload['event'];

            Log::info('Razorpay webhook received', ['event' => $event, 'payload' => $payload]);

            switch ($event) {
                case 'payment.captured':
                    $this->handlePaymentCaptured($payload['payload']['payment']['entity']);
                    break;
                case 'payment.failed':
                    $this->handlePaymentFailed($payload['payload']['payment']['entity']);
                    break;
                case 'order.paid':
                    $this->handleOrderPaid($payload['payload']['order']['entity']);
                    break;
                default:
                    Log::info('Unhandled webhook event', ['event' => $event]);
            }

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            Log::error('Webhook processing failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Webhook processing failed'
            ], 400);
        }
    }

    /**
     * Get payment status
     */
    public function getPaymentStatus(Request $request, SalesLedger $salesLedger): JsonResponse
    {
        $this->authorize('view', $salesLedger);

        try {
            // Check if there's a recent payment for this sales ledger
            $payment = $this->razorpay->payment->all([
                'count' => 1,
                'notes' => json_encode(['sales_ledger_id' => $salesLedger->id])
            ]);

            $paymentStatus = [
                'sales_ledger_id' => $salesLedger->id,
                'invoice_number' => $salesLedger->invoice_number,
                'payment_status' => $salesLedger->payment_status,
                'total_amount' => $salesLedger->total_customer_price,
                'payment_method' => $salesLedger->payment_method,
                'razorpay_payments' => $payment['items'] ?? [],
            ];

            return response()->json([
                'success' => true,
                'data' => $paymentStatus
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get payment status: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to get payment status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Refund payment
     */
    public function refundPayment(Request $request): JsonResponse
    {
        $request->validate([
            'payment_id' => 'required|string',
            'amount' => 'required|numeric|min:1',
            'reason' => 'required|string|max:255',
        ]);

        try {
            $refundData = [
                'amount' => $request->amount * 100, // Convert to paise
                'reason' => $request->reason,
            ];

            $refund = $this->razorpay->refund->create($refundData);

            return response()->json([
                'success' => true,
                'message' => 'Refund processed successfully',
                'data' => [
                    'refund_id' => $refund['id'],
                    'payment_id' => $refund['payment_id'],
                    'amount' => $refund['amount'] / 100,
                    'status' => $refund['status'],
                    'reason' => $refund['reason'],
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Refund failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Refund failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get payment history
     */
    public function paymentHistory(Request $request): JsonResponse
    {
        $this->authorize('view-any', SalesLedger::class);

        $startDate = $request->get('start_date', now()->startOfMonth());
        $endDate = $request->get('end_date', now()->endOfMonth());
        $status = $request->get('status', '');

        $query = SalesLedger::with(['user'])
            ->whereBetween('sales_date', [$startDate, $endDate]);

        if ($status) {
            $query->where('payment_status', $status);
        }

        $payments = $query->select([
                'id',
                'invoice_number',
                'user_id',
                'total_customer_price',
                'payment_status',
                'payment_method',
                'sales_date',
                'payment_link'
            ])
            ->orderBy('sales_date', 'desc')
            ->paginate($request->get('per_page', 15));

        $summary = [
            'total_payments' => $payments->total(),
            'total_amount' => $payments->sum('total_customer_price'),
            'paid_amount' => $payments->where('payment_status', 'paid')->sum('total_customer_price'),
            'pending_amount' => $payments->where('payment_status', 'pending')->sum('total_customer_price'),
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'payments' => $payments,
                'summary' => $summary
            ]
        ]);
    }

    /**
     * Handle payment captured webhook
     */
    private function handlePaymentCaptured($payment): void
    {
        try {
            $order = $this->razorpay->order->fetch($payment['order_id']);
            $salesLedger = SalesLedger::where('invoice_number', $order['receipt'])->first();

            if ($salesLedger) {
                DB::beginTransaction();
                try {
                    $salesLedger->update([
                        'payment_status' => 'paid',
                        'payment_method' => $payment['method'] ?? 'online'
                    ]);

                    $this->createPaymentTransaction($salesLedger, $payment);

                    DB::commit();
                    Log::info('Payment captured and processed', ['payment_id' => $payment['id']]);

                } catch (\Exception $e) {
                    DB::rollBack();
                    Log::error('Failed to process captured payment: ' . $e->getMessage());
                }
            }
        } catch (\Exception $e) {
            Log::error('Failed to handle payment captured webhook: ' . $e->getMessage());
        }
    }

    /**
     * Handle payment failed webhook
     */
    private function handlePaymentFailed($payment): void
    {
        try {
            $order = $this->razorpay->order->fetch($payment['order_id']);
            $salesLedger = SalesLedger::where('invoice_number', $order['receipt'])->first();

            if ($salesLedger) {
                $salesLedger->update(['payment_status' => 'failed']);
                Log::info('Payment failed', ['payment_id' => $payment['id']]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to handle payment failed webhook: ' . $e->getMessage());
        }
    }

    /**
     * Handle order paid webhook
     */
    private function handleOrderPaid($order): void
    {
        try {
            $salesLedger = SalesLedger::where('invoice_number', $order['receipt'])->first();

            if ($salesLedger) {
                $salesLedger->update(['payment_status' => 'paid']);
                Log::info('Order paid', ['order_id' => $order['id']]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to handle order paid webhook: ' . $e->getMessage());
        }
    }

    /**
     * Create payment transaction record
     */
    private function createPaymentTransaction(SalesLedger $salesLedger, $payment): void
    {
        $lastTransaction = Transaction::latest()->first();
        
        $bankBalance = $lastTransaction ? $lastTransaction->bank_balance : 0;
        $cashInHand = $lastTransaction ? $lastTransaction->cash_in_hand : 0;

        // Add payment amount to bank balance (online payments)
        $bankBalance += $salesLedger->total_customer_price;

        Transaction::create([
            'bank_balance' => $bankBalance,
            'cash_in_hand' => $cashInHand,
            'sales_ledger_id' => $salesLedger->id,
            'reason' => "Payment received: {$payment['id']} for invoice {$salesLedger->invoice_number}"
        ]);
    }
} 