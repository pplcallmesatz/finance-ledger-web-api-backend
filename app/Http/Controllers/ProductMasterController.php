<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Models\ProductMaster;
use App\Models\CategoryMaster;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\ProductMasterStoreRequest;
use App\Http\Requests\ProductMasterUpdateRequest;

class ProductMasterController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $this->authorize('view-any', ProductMaster::class);

        $search = (string) $request->get('search', '');
        $category = (string) $request->get('category', '');
        $categoryMasters = CategoryMaster::pluck('name', 'id');
        $productMasters = ProductMaster::search($search)
            ->when($category, function ($query, $category) {
                return $query->where('category_id', $category);
            })
            ->latest()
            ->paginate(50)
            ->withQueryString();
        return view(
            'app.product_masters.index',
            compact('productMasters', 'search','category','categoryMasters')
        );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request): View
    {
        $this->authorize('create', ProductMaster::class);

        $categoryMasters = CategoryMaster::select('name', 'id','self_life')->get();
        return view('app.product_masters.create', compact('categoryMasters'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductMasterStoreRequest $request): RedirectResponse
    {
        $this->authorize('create', ProductMaster::class);

        $validated = $request->validated();

        $category = CategoryMaster::find($validated['category_id']);
        $manufacturingDate = Carbon::parse($validated['manufacturing_date']);
        $purchaseDate = Carbon::parse($validated['purchase_date']);
        $productsPurchasedThisMonth = ProductMaster::query()
            ->where('category_id', $category->id)
            ->whereBetween('purchase_date', [$purchaseDate->copy()->startOfMonth(), $purchaseDate->copy()->endOfMonth()])
            ->count();
        $newBatchCount = str_pad($productsPurchasedThisMonth + 1, 4, 0, STR_PAD_LEFT);

        $validated['purchase_date'] = $purchaseDate;
        $validated['manufacturing_date'] = $manufacturingDate;
        $validated['batch_number'] = $category->symbol . '/' . $manufacturingDate->format('y'). $manufacturingDate->format('m'). $newBatchCount;
        $productMaster = ProductMaster::create($validated);

        return redirect()
            ->route('product-masters.edit', $productMaster)
            ->withSuccess(__('crud.common.created'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, ProductMaster $productMaster): View
    {
        $this->authorize('view', $productMaster);

        return view('app.product_masters.show', compact('productMaster'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, ProductMaster $productMaster): View
    {
        $this->authorize('update', $productMaster);

        $categoryMasters = CategoryMaster::select('name', 'id','self_life')->get();

        return view(
            'app.product_masters.edit',
            compact('productMaster', 'categoryMasters')
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(
        ProductMasterUpdateRequest $request,
        ProductMaster $productMaster
    ): RedirectResponse {
        $this->authorize('update', $productMaster);

        $validated = $request->validated();

        $productMaster->update($validated);

        return redirect()
            ->route('product-masters.edit', $productMaster)
            ->withSuccess(__('crud.common.saved'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(
        Request $request,
        ProductMaster $productMaster
    ): RedirectResponse {
        $this->authorize('delete', $productMaster);

        $productMaster->delete();

        return redirect()
            ->route('product-masters.index')
            ->withSuccess(__('crud.common.removed'));
    }
}
