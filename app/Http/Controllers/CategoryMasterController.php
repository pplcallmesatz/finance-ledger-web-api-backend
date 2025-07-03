<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Models\CategoryMaster;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\CategoryMasterStoreRequest;
use App\Http\Requests\CategoryMasterUpdateRequest;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CategoryMasterController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $this->authorize('view-any', CategoryMaster::class);

        $search = $request->get('search', '');

        $categoryMasters = CategoryMaster::search($search)
            ->latest()
            ->paginate(50)
            ->withQueryString();

        return view(
            'app.category_masters.index',
            compact('categoryMasters', 'search')
        );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request): View
    {
        $this->authorize('create', CategoryMaster::class);

        return view('app.category_masters.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CategoryMasterStoreRequest $request): RedirectResponse
    {
        $this->authorize('create', CategoryMaster::class);

        $validated = $request->validated();

        $categoryMaster = CategoryMaster::create($validated);

        return redirect()
            ->route('category-masters.edit', $categoryMaster)
            ->withSuccess(__('crud.common.created'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, CategoryMaster $categoryMaster): View
    {
        $this->authorize('view', $categoryMaster);

        // Fetch products in the category
        $products = DB::table('products')
            ->where('category_master_id', $categoryMaster->id)
            ->pluck('id');

        // Fetch sales data for products in this category
        $sales = DB::table('ledger_product')
            ->join('sales_ledgers', 'ledger_product.sales_ledger_id', '=', 'sales_ledgers.id')
            ->whereIn('ledger_product.product_id', $products)
            ->select('sales_ledgers.user_id', 'sales_ledgers.sales_date')
            ->get();

        // Group sales by user_id
        $salesByUser = $sales->groupBy('user_id');

        // Prepare user purchase data
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
            $userPurchaseData[] = [
                'name' => $user ? $user->name : 'Unknown User', // Get user name
                'last_purchase_date' => $lastPurchaseDate,
                'next_purchase_date' => $nextPurchaseDate,
                'average_days' => $averagePurchaseDays, // Include average days
            ];
        }

        // Sort user purchase data by last purchase date in descending order
        $userPurchaseData = collect($userPurchaseData)->sortByDesc('last_purchase_date')->values()->all();

        return view('app.category_masters.show', compact('categoryMaster', 'userPurchaseData'));
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
    public function edit(Request $request, CategoryMaster $categoryMaster): View
    {
        $this->authorize('update', $categoryMaster);

        return view('app.category_masters.edit', compact('categoryMaster'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(
        CategoryMasterUpdateRequest $request,
        CategoryMaster $categoryMaster
    ): RedirectResponse {
        $this->authorize('update', $categoryMaster);

        $validated = $request->validated();

        $categoryMaster->update($validated);

        return redirect()
            ->route('category-masters.edit', $categoryMaster)
            ->withSuccess(__('crud.common.saved'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(
        Request $request,
        CategoryMaster $categoryMaster
    ): RedirectResponse {
        $this->authorize('delete', $categoryMaster);

        $categoryMaster->delete();

        return redirect()
            ->route('category-masters.index')
            ->withSuccess(__('crud.common.removed'));
    }
}
