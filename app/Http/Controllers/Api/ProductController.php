<?php

namespace App\Http\Controllers\Api;

use App\Models\Product;
use App\Models\ProductMaster;
use App\Models\CategoryMaster;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Http\Resources\ProductCollection;
use App\Http\Requests\ProductStoreRequest;
use App\Http\Requests\ProductUpdateRequest;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): ProductCollection
    {
        $this->authorize('view-any', Product::class);

        $search = $request->get('search', '');
        $categoryId = $request->get('category_id', '');
        $barcode = $request->get('barcode', '');

        $products = Product::with(['categoryMaster'])
            ->search($search)
            ->when($categoryId, function ($query, $categoryId) {
                return $query->where('category_master_id', $categoryId);
            })
            ->when($barcode, function ($query, $barcode) {
                return $query->where('barcode', $barcode);
            })
            ->latest()
            ->get();

        return new ProductCollection($products);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductStoreRequest $request): JsonResponse
    {
        $this->authorize('create', Product::class);

        try {
            $validated = $request->validated();

            // Check if barcode already exists
            if (!empty($validated['barcode'])) {
                $existingProduct = Product::where('barcode', $validated['barcode'])->first();
                if ($existingProduct) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Product with this barcode already exists'
                    ], 422);
                }
            }

            // Calculate product price if not provided
            if (!isset($validated['product_price'])) {
                $validated['product_price'] = $validated['purchase_price'] + $validated['packing_price'];
            }

            $product = Product::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Product created successfully',
                'data' => new ProductResource($product->load('categoryMaster'))
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create product: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Product $product): ProductResource
    {
        $this->authorize('view', $product);

        return new ProductResource($product->load(['categoryMaster', 'salesLedgers']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProductUpdateRequest $request, Product $product): JsonResponse
    {
        $this->authorize('update', $product);

        try {
            $validated = $request->validated();

            // Check if barcode already exists (excluding current product)
            if (!empty($validated['barcode'])) {
                $existingProduct = Product::where('barcode', $validated['barcode'])
                    ->where('id', '!=', $product->id)
                    ->first();
                if ($existingProduct) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Product with this barcode already exists'
                    ], 422);
                }
            }

            // Calculate selling price if not provided
            if (empty($validated['selling_price'])) {
                $validated['selling_price'] = $validated['product_price'] + $validated['packing_price'];
            }

            $product->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Product updated successfully',
                'data' => new ProductResource($product->load('categoryMaster'))
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update product: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Product $product): JsonResponse
    {
        $this->authorize('delete', $product);

        try {
            // Check if product is used in any sales
            $salesCount = $product->salesLedgers()->count();
            if ($salesCount > 0) {
                return response()->json([
                    'success' => false,
                    'message' => "Cannot delete product. It is used in {$salesCount} sales records."
                ], 422);
            }

            $product->delete();

            return response()->json([
                'success' => true,
                'message' => 'Product deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete product: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Search product by barcode
     */
    public function searchByBarcode(Request $request): JsonResponse
    {
        $request->validate([
            'barcode' => 'required|string'
        ]);

        $product = Product::with(['categoryMaster'])
            ->where('barcode', $request->barcode)
            ->first();

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => new ProductResource($product)
        ]);
    }

    /**
     * Get product inventory status
     */
    public function inventoryStatus(Request $request, Product $product): JsonResponse
    {
        $this->authorize('view', $product);

        // Get available stock from product masters
        $availableStock = ProductMaster::where('category_id', $product->category_master_id)
            ->sum('total_piece');

        // Get sold quantity
        $soldQuantity = DB::table('ledger_product')
            ->join('sales_ledgers', 'ledger_product.sales_ledger_id', '=', 'sales_ledgers.id')
            ->where('ledger_product.product_id', $product->id)
            ->sum('ledger_product.quantity');

        // Get sales statistics
        $salesStats = DB::table('ledger_product')
            ->join('sales_ledgers', 'ledger_product.sales_ledger_id', '=', 'sales_ledgers.id')
            ->where('ledger_product.product_id', $product->id)
            ->select(
                DB::raw('COUNT(DISTINCT sales_ledgers.id) as total_orders'),
                DB::raw('SUM(ledger_product.quantity) as total_quantity_sold'),
                DB::raw('SUM(ledger_product.customer_price) as total_revenue'),
                DB::raw('AVG(ledger_product.customer_price) as average_price')
            )
            ->first();

        return response()->json([
            'success' => true,
            'data' => [
                'product' => new ProductResource($product),
                'inventory' => [
                    'available_stock' => $availableStock,
                    'sold_quantity' => $soldQuantity,
                    'remaining_stock' => $availableStock - $soldQuantity,
                ],
                'sales_statistics' => $salesStats,
            ]
        ]);
    }

    /**
     * Get product pricing history
     */
    public function pricingHistory(Request $request, Product $product): JsonResponse
    {
        $this->authorize('view', $product);

        $startDate = $request->get('start_date', now()->subMonths(6));
        $endDate = $request->get('end_date', now());

        $pricingHistory = DB::table('ledger_product')
            ->join('sales_ledgers', 'ledger_product.sales_ledger_id', '=', 'sales_ledgers.id')
            ->where('ledger_product.product_id', $product->id)
            ->whereBetween('sales_ledgers.sales_date', [$startDate, $endDate])
            ->select(
                'sales_ledgers.sales_date',
                'ledger_product.product_price',
                'ledger_product.selling_price',
                'ledger_product.customer_price',
                'ledger_product.quantity'
            )
            ->orderBy('sales_ledgers.sales_date')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'product' => new ProductResource($product),
                'pricing_history' => $pricingHistory,
                'summary' => [
                    'min_price' => $pricingHistory->min('customer_price'),
                    'max_price' => $pricingHistory->max('customer_price'),
                    'avg_price' => round($pricingHistory->avg('customer_price'), 2),
                ]
            ]
        ]);
    }

    /**
     * Bulk update product prices
     */
    public function bulkUpdatePrices(Request $request): JsonResponse
    {
        $this->authorize('update', Product::class);

        $request->validate([
            'products' => 'required|array',
            'products.*.id' => 'required|exists:products,id',
            'products.*.product_price' => 'required|numeric|min:0',
            'products.*.packing_price' => 'required|numeric|min:0',
            'products.*.selling_price' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $updatedCount = 0;

            foreach ($request->products as $productData) {
                $product = Product::find($productData['id']);
                if ($product) {
                    $product->update([
                        'product_price' => $productData['product_price'],
                        'packing_price' => $productData['packing_price'],
                        'selling_price' => $productData['selling_price'],
                    ]);
                    $updatedCount++;
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Successfully updated {$updatedCount} products",
                'data' => [
                    'updated_count' => $updatedCount
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update products: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get products with low stock
     */
    public function lowStockProducts(Request $request): JsonResponse
    {
        $this->authorize('view-any', Product::class);

        $threshold = $request->get('threshold', 10);

        $lowStockProducts = Product::with(['categoryMaster'])
            ->whereHas('categoryMaster.productMasters', function ($query) use ($threshold) {
                $query->where('total_piece', '<', $threshold);
            })
            ->get()
            ->map(function ($product) {
                $product->available_stock = $product->categoryMaster->productMasters->sum('total_piece');
                return $product;
            })
            ->filter(function ($product) use ($threshold) {
                return $product->available_stock < $threshold;
            });

        return response()->json([
            'success' => true,
            'data' => [
                'products' => ProductResource::collection($lowStockProducts),
                'threshold' => $threshold,
                'count' => $lowStockProducts->count()
            ]
        ]);
    }

    /**
     * Get product performance analytics
     */
    public function performanceAnalytics(Request $request, Product $product): JsonResponse
    {
        $this->authorize('view', $product);

        $startDate = $request->get('start_date', now()->startOfMonth());
        $endDate = $request->get('end_date', now()->endOfMonth());

        $analytics = DB::table('ledger_product')
            ->join('sales_ledgers', 'ledger_product.sales_ledger_id', '=', 'sales_ledgers.id')
            ->where('ledger_product.product_id', $product->id)
            ->whereBetween('sales_ledgers.sales_date', [$startDate, $endDate])
            ->select(
                DB::raw('DATE(sales_ledgers.sales_date) as date'),
                DB::raw('SUM(ledger_product.quantity) as quantity_sold'),
                DB::raw('SUM(ledger_product.customer_price) as revenue'),
                DB::raw('COUNT(DISTINCT sales_ledgers.id) as order_count')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $totalRevenue = $analytics->sum('revenue');
        $totalQuantity = $analytics->sum('quantity_sold');
        $totalOrders = $analytics->sum('order_count');

        return response()->json([
            'success' => true,
            'data' => [
                'product' => new ProductResource($product),
                'daily_analytics' => $analytics,
                'summary' => [
                    'total_revenue' => $totalRevenue,
                    'total_quantity_sold' => $totalQuantity,
                    'total_orders' => $totalOrders,
                    'average_order_value' => $totalOrders > 0 ? round($totalRevenue / $totalOrders, 2) : 0,
                    'average_quantity_per_order' => $totalOrders > 0 ? round($totalQuantity / $totalOrders, 2) : 0,
                ]
            ]
        ]);
    }

    /**
     * Generate barcode for product
     */
    public function generateBarcode(Request $request, Product $product): JsonResponse
    {
        $this->authorize('update', $product);

        try {
            // Generate a unique barcode
            $barcode = $this->generateUniqueBarcode();
            
            $product->update(['barcode' => $barcode]);

            return response()->json([
                'success' => true,
                'message' => 'Barcode generated successfully',
                'data' => [
                    'barcode' => $barcode,
                    'product' => new ProductResource($product)
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate barcode: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate unique barcode
     */
    private function generateUniqueBarcode(): string
    {
        do {
            $barcode = 'P' . str_pad(rand(1, 999999), 6, '0', STR_PAD_LEFT);
        } while (Product::where('barcode', $barcode)->exists());

        return $barcode;
    }
}
