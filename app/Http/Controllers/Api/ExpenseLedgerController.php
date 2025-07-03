<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\ExpenseLedger;
use App\Http\Controllers\Controller;
use App\Http\Resources\ExpenseLedgerResource;
use App\Http\Resources\ExpenseLedgerCollection;
use App\Http\Requests\ExpenseLedgerStoreRequest;
use App\Http\Requests\ExpenseLedgerUpdateRequest;

class ExpenseLedgerController extends Controller
{
    public function index(Request $request): ExpenseLedgerCollection
    {
        $this->authorize('view-any', ExpenseLedger::class);

        $search = $request->get('search', '');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        $expenseLedgers = ExpenseLedger::search($search)
            ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                return $query->whereBetween('purchase_date', [$startDate, $endDate]);
            })
            ->latest()
            ->get();

        return new ExpenseLedgerCollection($expenseLedgers);
    }

    public function store(
        ExpenseLedgerStoreRequest $request
    ): ExpenseLedgerResource {
        $this->authorize('create', ExpenseLedger::class);

        $validated = $request->validated();

        $expenseLedger = ExpenseLedger::create($validated);

        // Create a transaction if 'deduct' is set
        if ($expenseLedger->deduct === 'deduct') {
            // Get the last transaction to determine current balances
            $lastTransaction = \App\Models\Transaction::latest()->first();
            $bankBalance = $lastTransaction ? $lastTransaction->bank_balance : 0;
            $cashInHand = $lastTransaction ? $lastTransaction->cash_in_hand : 0;

            // Deduct from the appropriate account
            if ($expenseLedger->payment_method === 'cash') {
                $cashInHand -= $expenseLedger->purchase_price;
            } else {
                $bankBalance -= $expenseLedger->purchase_price;
            }

            \App\Models\Transaction::create([
                'expense_ledger_id' => $expenseLedger->id,
                'bank_balance' => $bankBalance,
                'cash_in_hand' => $cashInHand,
                'reason' => "Expense: {$expenseLedger->invoice_number}"
            ]);
        }

        return new ExpenseLedgerResource($expenseLedger);
    }

    public function show(
        Request $request,
        ExpenseLedger $expenseLedger
    ): ExpenseLedgerResource {
        $this->authorize('view', $expenseLedger);

        return new ExpenseLedgerResource($expenseLedger);
    }

    public function update(
        ExpenseLedgerUpdateRequest $request,
        ExpenseLedger $expenseLedger
    ): ExpenseLedgerResource {
        $this->authorize('update', $expenseLedger);

        $validated = $request->validated();

        $expenseLedger->update($validated);

        return new ExpenseLedgerResource($expenseLedger);
    }

    public function destroy(
        Request $request,
        ExpenseLedger $expenseLedger
    ): Response {
        $this->authorize('delete', $expenseLedger);

        $expenseLedger->delete();

        return response()->noContent();
    }
}
