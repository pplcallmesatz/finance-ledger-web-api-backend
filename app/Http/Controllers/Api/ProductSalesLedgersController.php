<?php
namespace App\Http\Controllers\Api;

use App\Models\Product;
use App\Models\SalesLedger;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Http\Resources\SalesLedgerCollection;

class ProductSalesLedgersController extends Controller
{
    public function index(
        Request $request,
        Product $product
    ): SalesLedgerCollection {
        $this->authorize('view', $product);

        $search = $request->get('search', '');

        $salesLedgers = $product
            ->salesLedgers()
            ->search($search)
            ->latest()
            ->paginate();

        return new SalesLedgerCollection($salesLedgers);
    }

    public function store(
        Request $request,
        Product $product,
        SalesLedger $salesLedger
    ): Response {
        $this->authorize('update', $product);

        $product->salesLedgers()->syncWithoutDetaching([$salesLedger->id]);

        return response()->noContent();
    }

    public function destroy(
        Request $request,
        Product $product,
        SalesLedger $salesLedger
    ): Response {
        $this->authorize('update', $product);

        $product->salesLedgers()->detach($salesLedger);

        return response()->noContent();
    }
}
