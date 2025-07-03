<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Models\CategoryMaster;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\ProductStoreRequest;
use App\Http\Requests\ProductUpdateRequest;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\User;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $this->authorize('view-any', Product::class);

        $search = (string) $request->get('search', '');
        $category = (string) $request->get('category', '');
        $categoryMasters = CategoryMaster::pluck('name', 'id');
        $products = Product::search($search)
            ->when($category, function ($query, $category) {
                return $query->where('category_master_id', $category);
            })    
            ->latest()
            ->paginate(50)
            ->withQueryString();

        return view('app.products.index', compact('products', 'search', 'categoryMasters'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request): View
    {
        $this->authorize('create', Product::class);

        $categoryMasters = CategoryMaster::pluck('name', 'id');

        return view('app.products.create', compact('categoryMasters'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductStoreRequest $request): RedirectResponse
    {
        
        $this->authorize('create', Product::class);

        $validated = $request->validated();
        $validated['product_price'] = $validated['purchase_price'] + $validated['packing_price'];
        $product = Product::create($validated);

        return redirect()
            ->route('products.edit', $product)
            ->withSuccess(__('crud.common.created'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Product $product): View
    {
        $this->authorize('view', $product);

        // Fetch customers who purchased this product
        $sales = DB::table('ledger_product')
            ->join('sales_ledgers', 'ledger_product.sales_ledger_id', '=', 'sales_ledgers.id')
            ->where('ledger_product.product_id', $product->id)
            ->select('sales_ledgers.user_id', 'sales_ledgers.sales_date')
            ->get();

        // Group sales by user_id
        $salesByUser = $sales->groupBy('user_id');

        // Calculate average purchase days and predict next purchase date for each user
        $userPurchaseData = [];
        foreach ($salesByUser as $userId => $userSales) {
            $purchaseDates = $userSales->pluck('sales_date')->map(function ($date) {
                return Carbon::parse($date); // Convert string to Carbon instance
            });

            // Calculate average purchase days
            $averagePurchaseDays = $this->calculateAveragePurchaseDays($purchaseDates);

            // Get the last purchase date
            $lastPurchaseDate = $purchaseDates->last();

            // Predict the next purchase date
            $nextPurchaseDate = $lastPurchaseDate ? $lastPurchaseDate->copy()->addDays($averagePurchaseDays) : null;

            // Fetch user name
            $user = User::find($userId); // Assuming user ID is the primary key
            $userPurchaseData[$userId] = [
                'name' => $user ? $user->name : 'Unknown User', // Get user name
                'average_days' => $averagePurchaseDays,
                'last_purchase_date' => $lastPurchaseDate,
                'next_purchase_date' => $nextPurchaseDate,
            ];
        }

        // Sort user purchase data by next purchase date in descending order
        $userPurchaseData = collect($userPurchaseData)->sortByDesc('next_purchase_date')->values()->all();

        return view('app.products.show', compact('product', 'sales', 'userPurchaseData'));
    }

    private function calculateAveragePurchaseDays($dates)
    {
        if ($dates->isEmpty()) {
            return 0; // No purchases
        }

        $dateDiffs = [];
        for ($i = 1; $i < $dates->count(); $i++) {
            $dateDiffs[] = $dates[$i]->diffInDays($dates[$i - 1]);
        }

        // Use ceil to round up the average days
        return $dateDiffs ? ceil(array_sum($dateDiffs) / count($dateDiffs)) : 0;
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, Product $product): View
    {
        $this->authorize('update', $product);

        $categoryMasters = CategoryMaster::pluck('name', 'id');

        return view('app.products.edit', compact('product', 'categoryMasters'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(
        ProductUpdateRequest $request,
        Product $product
    ): RedirectResponse {
        $this->authorize('update', $product);
        $validated = $request->validated();
        $validated['product_price'] = $validated['purchase_price'] + $validated['packing_price'];
        $product->update($validated);

        return redirect()
            ->route('products.edit', $product)
            ->withSuccess(__('crud.common.saved'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(
        Request $request,
        Product $product
    ): RedirectResponse {
        $this->authorize('delete', $product);

        $product->delete();

        return redirect()
            ->route('products.index')
            ->withSuccess(__('crud.common.removed'));
    }
    public function getCategories()
    {
        $categories = Category::all();
        return view('categories.index', compact('categories'));
    }

    public function getProductsByCategory($categoryId)
    {
        $products = Product::where('category_id', $categoryId)->get();
        return response()->json($products);
    }
}
