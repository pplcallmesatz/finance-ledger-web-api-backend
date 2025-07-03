<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\ExpenseLedger;
use App\Http\Controllers\Controller;
use App\Http\Resources\TransactionResource;
use App\Http\Resources\TransactionCollection;

class ExpenseLedgerTransactionsController extends Controller
{
    public function index(
        Request $request,
        ExpenseLedger $expenseLedger
    ): TransactionCollection {
        $this->authorize('view', $expenseLedger);

        $search = $request->get('search', '');

        $transactions = $expenseLedger
            ->transactions()
            ->search($search)
            ->latest()
            ->paginate();

        return new TransactionCollection($transactions);
    }

    public function store(
        Request $request,
        ExpenseLedger $expenseLedger
    ): TransactionResource {
        $this->authorize('create', Transaction::class);

        $validated = $request->validate([
            'bank_balance' => ['required', 'numeric'],
            'cash_in_hand' => ['required', 'numeric'],
            'reason' => ['nullable', 'max:255', 'string'],
        ]);

        $transaction = $expenseLedger->transactions()->create($validated);

        return new TransactionResource($transaction);
    }
}
