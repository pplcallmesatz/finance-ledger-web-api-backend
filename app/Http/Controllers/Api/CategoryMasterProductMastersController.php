<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\CategoryMaster;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductMasterResource;
use App\Http\Resources\ProductMasterCollection;

class CategoryMasterProductMastersController extends Controller
{
    public function index(
        Request $request,
        CategoryMaster $categoryMaster
    ): ProductMasterCollection {
        $this->authorize('view', $categoryMaster);

        $search = $request->get('search', '');

        $productMasters = $categoryMaster
            ->productMasters()
            ->search($search)
            ->latest()
            ->paginate();

        return new ProductMasterCollection($productMasters);
    }

    public function store(
        Request $request,
        CategoryMaster $categoryMaster
    ): ProductMasterResource {
        $this->authorize('create', ProductMaster::class);

        $validated = $request->validate([
            'name' => ['required', 'max:255', 'string'],
            'purchase_price' => ['required', 'numeric'],
            'purchase_date' => ['required', 'date'],
            'manufacturing_date' => ['required', 'date'],
            'transportation_cost' => ['required', 'numeric'],
            'invoice_number' => ['required', 'max:255', 'string'],
            'quantity_purchased' => ['required', 'numeric'],
            'vendor' => ['nullable', 'max:255', 'string'],
        ]);

        $productMaster = $categoryMaster->productMasters()->create($validated);

        return new ProductMasterResource($productMaster);
    }
}
