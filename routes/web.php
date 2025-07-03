<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthLoginController;
use App\Http\Controllers\HomeProductListController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PaymentPendingListController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SalesLedgerController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\ProductMasterController;
use App\Http\Controllers\ExpenseLedgerController;
use App\Http\Controllers\CategoryMasterController;
use App\Http\Controllers\PaymentController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [HomeProductListController::class, 'index']);

// Auth::routes();

// Route::get('/home', [HomeController::class, 'index'])->name('home');

// Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
// Route::post('login', [LoginController::class, 'login']);
// Route::post('logout', [LoginController::class, 'logout'])->name('logout');

// Route::group(['middleware' => 'guest'], function () {
//     Route::get('/register', [AuthController::class, 'register'])->name('register');
//     Route::post('/register', [AuthController::class, 'registerPost'])->name('register');
//     Route::get('/login', [AuthController::class, 'login'])->name('login');
//     Route::post('/login', [AuthController::class, 'loginPost'])->name('login');
// });


    // Route::get('/dashboard', function () {
    //     return view('app.dashboard.index');
    // })->middleware(['auth', 'verified'])->name('dashboard');
    
    Route::middleware('auth')->group(function () {

        Route::prefix('/')->group(function () {
            Route::resource('dashboard', DashboardController::class);
            Route::get('/sales-ledgers/pending-amount', [PaymentPendingListController::class,'index'])->name('pending');;
            Route::resource('category-masters', CategoryMasterController::class);
            Route::resource('product-masters', ProductMasterController::class);
            Route::resource('products', ProductController::class);
            Route::resource('users', UserController::class);
            Route::resource('expense-ledgers', ExpenseLedgerController::class);
            Route::resource('sales-ledgers', SalesLedgerController::class);
            Route::resource('transactions', TransactionController::class);
            Route::get('/get-products/{product_id}/{module}', [SalesLedgerController::class, 'getProducts'])->name('products.get');
            Route::get('/product-masters/{id}', [ProductMasterController::class, 'show'])->name('product-masters.show');
            Route::get('/products/{id}', [ProductController::class, 'show'])->name('products.show');
            Route::post('/create-payment-link', [PaymentController::class, 'createPaymentLink'])->name('payment.create');

    });

        // Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        // Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        // Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    });
    
    require __DIR__.'/auth.php';
    