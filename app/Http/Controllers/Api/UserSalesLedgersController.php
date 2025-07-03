<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\SalesLedgerResource;
use App\Http\Resources\SalesLedgerCollection;

class UserSalesLedgersController extends Controller
{
    public function index(Request $request, User $user): SalesLedgerCollection
    {
        $this->authorize('view', $user);

        $search = $request->get('search', '');

        $salesLedgers = $user
            ->salesLedgers()
            ->search($search)
            ->latest()
            ->paginate();

        return new SalesLedgerCollection($salesLedgers);
    }

    public function store(Request $request, User $user): SalesLedgerResource
    {
        $this->authorize('create', SalesLedger::class);

        $validated = $request->validate([
            'total_product_price' => ['required', 'numeric'],
            'selling_product_price' => ['required', 'numeric'],
            'payment_status' => ['required', 'max:255', 'string'],
            'remarks' => ['nullable', 'max:255', 'string'],
            'company_address' => ['nullable', 'max:255', 'string'],
            'invoice_number' => ['nullable', 'max:255', 'string'],
            'payment_method' => ['required', 'max:255', 'string'],
        ]);

        $salesLedger = $user->salesLedgers()->create($validated);

        return new SalesLedgerResource($salesLedger);
    }
}
