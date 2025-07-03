<?php

namespace App\Http\Controllers;
use Illuminate\View\View;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\SalesLedger;
use App\Models\ExpenseLedger;
use App\Models\Transaction;
use App\Models\Product;
use App\Models\ProductMaster;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    //

    public function index(Request $request): View
    {
        $user = Auth::user();
        $userName = $user ? $user->name : 'Guest';


        $search = $request->get('search', '');
        $transactions = Transaction::query()->latest('id')->first();
         // $this->authorize('view', $user);
         $totalExpenses = ExpenseLedger::sum('purchase_price');
         $totalSales = SalesLedger::sum('total_customer_price');
         $totalPending = SalesLedger::where('payment_status', 'pending')
         ->sum('total_customer_price');
         
         
         // Total Profit 
         $totalProductPrice = SalesLedger::sum('total_product_price');
         $totalProfit = $totalSales - $totalProductPrice;
         

        //START: Product Profit / Loss 

        $productsSummary = DB::table('ledger_product')
        ->select('product_id', DB::raw('SUM(product_price) as total_product_price'), DB::raw('SUM(customer_price) as total_customer_price'))
        ->groupBy('product_id')
        ->get();

        // Step 2: Fetch product details with category details
        $productsSummary = $productsSummary->map(function ($productSummary) {
            $product = Product::with('categoryMaster')->find($productSummary->product_id);
            
            $productSummary->product_name = $product ? $product->name : 'Unknown Product';
            $productSummary->category_name = $product && $product->categoryMaster ? $product->categoryMaster->name : 'Unknown Category';
            $productSummary -> category_id = $product->categoryMaster->id;
            return $productSummary;
        });
        // Step 3: Grouping by product_id (no change)
        
        
        $groupedProductSums = $productsSummary->groupBy('category_id');
        // Calculate the overall sums
        $overallProductPrice = $productsSummary->sum('total_product_price');
        $overallCustomerPrice = $productsSummary->sum('total_customer_price');
        $overallPrice = [];
        $overallPrice['ProductPrice'] = $overallProductPrice;
        $overallPrice['CustomerPrice'] = $overallCustomerPrice;
        //END: Product Profit / Loss 
        
        $categoryMasters = DB::table('category_masters')
            ->leftJoin('product_masters', 'category_masters.id', '=', 'product_masters.category_id')
            ->select('category_masters.name', DB::raw('SUM(product_masters.total_piece) as total_available_quantity'))
            ->groupBy('category_masters.id')
            ->orderBy('total_available_quantity', 'asc')
            ->get();
        return view(
            'app.dashboard.index' , compact( 'userName', 'totalPending', 'totalSales', 'transactions', 'totalExpenses','totalProfit', 'groupedProductSums', 'overallPrice', 'categoryMasters')
        );
    }
}
