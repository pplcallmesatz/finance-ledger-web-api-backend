<?php

namespace App\Http\Controllers\Api;

use App\Models\SalesLedger;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\TransactionResource;
use App\Http\Resources\TransactionCollection;

class SalesLedgerTransactionsController extends Controller
{
    public function index(
        Request $request,
        SalesLedger $salesLedger
    ): TransactionCollection {
        $this->authorize('view', $salesLedger);

        $search = $request->get('search', '');

        $transactions = $salesLedger
            ->transactions()
            ->search($search)
            ->latest()
            ->paginate();

        return new TransactionCollection($transactions);
    }

    public function store(
        Request $request,
        SalesLedger $salesLedger
    ): TransactionResource {
        $this->authorize('create', Transaction::class);

        $validated = $request->validate([
            'bank_balance' => ['required', 'numeric'],
            'cash_in_hand' => ['required', 'numeric'],
            'reason' => ['nullable', 'max:255', 'string'],
        ]);

        $transaction = $salesLedger->transactions()->create($validated);

        return new TransactionResource($transaction);
    }
}
