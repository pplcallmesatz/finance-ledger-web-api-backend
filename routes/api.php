<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\SalesLedgerController;
use App\Http\Controllers\Api\TransactionController;
use App\Http\Controllers\Api\ProductMasterController;
use App\Http\Controllers\Api\ExpenseLedgerController;
use App\Http\Controllers\Api\CategoryMasterController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\UserSalesLedgersController;
use App\Http\Controllers\Api\ProductSalesLedgersController;
use App\Http\Controllers\Api\SalesLedgerProductsController;
use App\Http\Controllers\Api\CategoryMasterProductsController;
use App\Http\Controllers\Api\SalesLedgerTransactionsController;
use App\Http\Controllers\Api\ExpenseLedgerTransactionsController;
use App\Http\Controllers\Api\CategoryMasterProductMastersController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Public routes
Route::post('/login', [AuthController::class, 'login'])->name('api.login');

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    
    // User profile
    Route::get('/user', function (Request $request) {
        return $request->user();
    })->name('api.user');

    // Dashboard routes
    Route::prefix('dashboard')->name('dashboard.')->group(function () {
        Route::get('/overview', [DashboardController::class, 'overview'])->name('overview');
        Route::get('/product-analysis', [DashboardController::class, 'productAnalysis'])->name('product-analysis');
        Route::get('/inventory-status', [DashboardController::class, 'inventoryStatus'])->name('inventory-status');
        Route::get('/sales-trends', [DashboardController::class, 'salesTrends'])->name('sales-trends');
        Route::get('/top-products', [DashboardController::class, 'topProducts'])->name('top-products');
        Route::get('/customer-analytics', [DashboardController::class, 'customerAnalytics'])->name('customer-analytics');
    });

    // Payment routes
    Route::prefix('payments')->name('payments.')->group(function () {
        Route::post('/create-order', [PaymentController::class, 'createOrder'])->name('create-order');
        Route::post('/verify', [PaymentController::class, 'verifyPayment'])->name('verify');
        Route::post('/webhook', [PaymentController::class, 'webhook'])->name('webhook');
        Route::get('/history', [PaymentController::class, 'paymentHistory'])->name('history');
        Route::post('/refund', [PaymentController::class, 'refundPayment'])->name('refund');
        Route::get('/status/{salesLedger}', [PaymentController::class, 'getPaymentStatus'])->name('status');
    });

    // Category Master routes
    Route::apiResource('category-masters', CategoryMasterController::class);

    // CategoryMaster Product Masters
    Route::get('/category-masters/{categoryMaster}/product-masters', [
        CategoryMasterProductMastersController::class, 'index'
    ])->name('category-masters.product-masters.index');
    Route::post('/category-masters/{categoryMaster}/product-masters', [
        CategoryMasterProductMastersController::class, 'store'
    ])->name('category-masters.product-masters.store');

    // CategoryMaster Products
    Route::get('/category-masters/{categoryMaster}/products', [
        CategoryMasterProductsController::class, 'index'
    ])->name('category-masters.products.index');
    Route::post('/category-masters/{categoryMaster}/products', [
        CategoryMasterProductsController::class, 'store'
    ])->name('category-masters.products.store');

    // Product Master routes
    Route::apiResource('product-masters', ProductMasterController::class);

    // Product routes
    Route::apiResource('products', ProductController::class);
    Route::get('/products/search/barcode', [ProductController::class, 'searchByBarcode'])->name('products.search-barcode');
    Route::get('/products/{product}/inventory', [ProductController::class, 'inventoryStatus'])->name('products.inventory');
    Route::get('/products/{product}/pricing-history', [ProductController::class, 'pricingHistory'])->name('products.pricing-history');
    Route::get('/products/{product}/performance', [ProductController::class, 'performanceAnalytics'])->name('products.performance');
    Route::post('/products/bulk-update-prices', [ProductController::class, 'bulkUpdatePrices'])->name('products.bulk-update-prices');
    Route::get('/products/low-stock', [ProductController::class, 'lowStockProducts'])->name('products.low-stock');
    Route::post('/products/{product}/generate-barcode', [ProductController::class, 'generateBarcode'])->name('products.generate-barcode');

    // Product Sales Ledgers
    Route::get('/products/{product}/sales-ledgers', [
        ProductSalesLedgersController::class, 'index'
    ])->name('products.sales-ledgers.index');
    Route::post('/products/{product}/sales-ledgers/{salesLedger}', [
        ProductSalesLedgersController::class, 'store'
    ])->name('products.sales-ledgers.store');
    Route::delete('/products/{product}/sales-ledgers/{salesLedger}', [
        ProductSalesLedgersController::class, 'destroy'
    ])->name('products.sales-ledgers.destroy');

    // User routes
    Route::apiResource('users', UserController::class);

    // User Sales Ledgers
    Route::get('/users/{user}/sales-ledgers', [
        UserSalesLedgersController::class, 'index'
    ])->name('users.sales-ledgers.index');
    Route::post('/users/{user}/sales-ledgers', [
        UserSalesLedgersController::class, 'store'
    ])->name('users.sales-ledgers.store');

    // Expense Ledger routes
    Route::apiResource('expense-ledgers', ExpenseLedgerController::class);

    // ExpenseLedger Transactions
    Route::get('/expense-ledgers/{expenseLedger}/transactions', [
        ExpenseLedgerTransactionsController::class, 'index'
    ])->name('expense-ledgers.transactions.index');
    Route::post('/expense-ledgers/{expenseLedger}/transactions', [
        ExpenseLedgerTransactionsController::class, 'store'
    ])->name('expense-ledgers.transactions.store');

    // Add this new route for pending sales ledgers
    Route::get('/sales-ledgers/pending', [SalesLedgerController::class, 'pending'])->name('sales-ledgers.pending');

    // Sales Ledger routes
    Route::apiResource('sales-ledgers', SalesLedgerController::class);
    Route::patch('/sales-ledgers/{salesLedger}/payment-status', [SalesLedgerController::class, 'updatePaymentStatus'])->name('sales-ledgers.update-payment-status');
    Route::post('/sales-ledgers/{salesLedger}/payment-link', [SalesLedgerController::class, 'createPaymentLink'])->name('sales-ledgers.create-payment-link');
    Route::get('/sales-ledgers/summary', [SalesLedgerController::class, 'summary'])->name('sales-ledgers.summary');

    // SalesLedger Transactions
    Route::get('/sales-ledgers/{salesLedger}/transactions', [
        SalesLedgerTransactionsController::class, 'index'
    ])->name('sales-ledgers.transactions.index');
    Route::post('/sales-ledgers/{salesLedger}/transactions', [
        SalesLedgerTransactionsController::class, 'store'
    ])->name('sales-ledgers.transactions.store');

    // SalesLedger Products
    Route::get('/sales-ledgers/{salesLedger}/products', [
        SalesLedgerProductsController::class, 'index'
    ])->name('sales-ledgers.products.index');
    Route::post('/sales-ledgers/{salesLedger}/products/{product}', [
        SalesLedgerProductsController::class, 'store'
    ])->name('sales-ledgers.products.store');
    Route::delete('/sales-ledgers/{salesLedger}/products/{product}', [
        SalesLedgerProductsController::class, 'destroy'
    ])->name('sales-ledgers.products.destroy');

    // Transaction routes
    Route::apiResource('transactions', TransactionController::class);

    // Add this new route for updating payment info only
    Route::patch('/sales-ledgers/{id}/payment-info', [SalesLedgerController::class, 'updatePaymentInfo'])->name('sales-ledgers.update-payment-info');

    // User details API for detailed screen
    Route::get('/users/{id}/details', [UserController::class, 'userDetails'])->name('users.details');
});
