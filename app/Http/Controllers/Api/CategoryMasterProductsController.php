<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\CategoryMaster;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Http\Resources\ProductCollection;

class CategoryMasterProductsController extends Controller
{
    public function index(
        Request $request,
        CategoryMaster $categoryMaster
    ): ProductCollection {
        $this->authorize('view', $categoryMaster);

        $search = $request->get('search', '');

        $products = $categoryMaster
            ->products()
            ->search($search)
            ->latest()
            ->paginate();

        return new ProductCollection($products);
    }

    public function store(
        Request $request,
        CategoryMaster $categoryMaster
    ): ProductResource {
        $this->authorize('create', Product::class);

        $validated = $request->validate([
            'name' => ['required', 'max:255', 'string'],
            'purchase_price' => ['required', 'numeric'],
            'transport_charge' => ['required', 'numeric'],
            'packing_price' => ['required', 'numeric'],
            'product_price' => ['required', 'numeric'],
            'selling_price' => ['required', 'numeric'],
            'description' => ['nullable', 'max:255', 'string'],
        ]);

        $product = $categoryMaster->products()->create($validated);

        return new ProductResource($product);
    }
}
