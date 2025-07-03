<?php
namespace App\Http\Controllers\Api;

use App\Models\Product;
use App\Models\SalesLedger;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductCollection;

class SalesLedgerProductsController extends Controller
{
    public function index(
        Request $request,
        SalesLedger $salesLedger
    ): ProductCollection {
        $this->authorize('view', $salesLedger);

        $search = $request->get('search', '');

        $products = $salesLedger
            ->products()
            ->search($search)
            ->latest()
            ->paginate();

        return new ProductCollection($products);
    }

    public function store(
        Request $request,
        SalesLedger $salesLedger,
        Product $product
    ): Response {
        $this->authorize('update', $salesLedger);

        $salesLedger->products()->syncWithoutDetaching([$product->id]);

        return response()->noContent();
    }

    public function destroy(
        Request $request,
        SalesLedger $salesLedger,
        Product $product
    ): Response {
        $this->authorize('update', $salesLedger);

        $salesLedger->products()->detach($product);

        return response()->noContent();
    }
}
