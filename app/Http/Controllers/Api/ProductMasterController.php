<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\ProductMaster;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductMasterResource;
use App\Http\Resources\ProductMasterCollection;
use App\Http\Requests\ProductMasterStoreRequest;
use App\Http\Requests\ProductMasterUpdateRequest;

class ProductMasterController extends Controller
{
    public function index(Request $request): ProductMasterCollection
    {
        $this->authorize('view-any', ProductMaster::class);

        $search = $request->get('search', '');
        $categoryId = $request->get('category_id', '');

        $productMasters = ProductMaster::search($search)
            ->when($categoryId, function ($query, $categoryId) {
                return $query->where('category_id', $categoryId);
            })
            ->latest()
            ->paginate();

        return new ProductMasterCollection($productMasters);
    }

    public function store(
        ProductMasterStoreRequest $request
    ): ProductMasterResource {
        $this->authorize('create', ProductMaster::class);

        $validated = $request->validated();

        // Auto-generate batch_number (same as web logic)
        $category = \App\Models\CategoryMaster::find($validated['category_id']);
        $manufacturingDate = \Carbon\Carbon::parse($validated['manufacturing_date']);
        $purchaseDate = \Carbon\Carbon::parse($validated['purchase_date']);
        $productsPurchasedThisMonth = \App\Models\ProductMaster::query()
            ->where('category_id', $category->id)
            ->whereBetween('purchase_date', [$purchaseDate->copy()->startOfMonth(), $purchaseDate->copy()->endOfMonth()])
            ->count();
        $newBatchCount = str_pad($productsPurchasedThisMonth + 1, 4, 0, STR_PAD_LEFT);
        $validated['batch_number'] = $category->symbol . '/' . $manufacturingDate->format('y') . $manufacturingDate->format('m') . $newBatchCount;

        // Auto-calculate expire_date if not provided
        if (empty($validated['expire_date']) && !empty($category->self_life)) {
            $expireDate = $manufacturingDate->copy()->addMonths($category->self_life)->subDay();
            $validated['expire_date'] = $expireDate->toDateString();
        }

        $productMaster = ProductMaster::create($validated);

        return new ProductMasterResource($productMaster);
    }

    public function show(
        Request $request,
        ProductMaster $productMaster
    ): ProductMasterResource {
        $this->authorize('view', $productMaster);

        return new ProductMasterResource($productMaster);
    }

    public function update(
        ProductMasterUpdateRequest $request,
        ProductMaster $productMaster
    ): ProductMasterResource {
        $this->authorize('update', $productMaster);

        $validated = $request->validated();

        // Auto-generate batch_number (same as in store)
        $category = \App\Models\CategoryMaster::find($validated['category_id']);
        $manufacturingDate = \Carbon\Carbon::parse($validated['manufacturing_date']);
        $purchaseDate = \Carbon\Carbon::parse($validated['purchase_date']);
        $productsPurchasedThisMonth = \App\Models\ProductMaster::query()
            ->where('category_id', $category->id)
            ->whereBetween('purchase_date', [$purchaseDate->copy()->startOfMonth(), $purchaseDate->copy()->endOfMonth()])
            ->count();
        $newBatchCount = str_pad($productsPurchasedThisMonth + 1, 4, 0, STR_PAD_LEFT);
        $validated['batch_number'] = $category->symbol . '/' . $manufacturingDate->format('y') . $manufacturingDate->format('m') . $newBatchCount;

        $productMaster->update($validated);

        return new ProductMasterResource($productMaster);
    }

    public function destroy(
        Request $request,
        ProductMaster $productMaster
    ): Response {
        $this->authorize('delete', $productMaster);

        $productMaster->delete();

        return response()->noContent();
    }
}
