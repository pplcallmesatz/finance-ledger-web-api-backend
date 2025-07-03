<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Models\ExpenseLedger;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\ExpenseLedgerStoreRequest;
use App\Http\Requests\ExpenseLedgerUpdateRequest;
use App\Models\Transaction;

class ExpenseLedgerController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function index(Request $request): View
    {
        $this->authorize('view-any', ExpenseLedger::class);        
        $search = $request->get('search', '');

        $expenseLedger = $this -> returnRedirection();
        $expenseLedgers = $expenseLedger[0];
        $search = "";

        return view(
            'app.expense_ledgers.index',
            compact('expenseLedgers', 'search')
        );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request): View
    {
        $this->authorize('create', ExpenseLedger::class);

        return view('app.expense_ledgers.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ExpenseLedgerStoreRequest $request): View
    {
        $this->authorize('create', ExpenseLedger::class);

        $validated = $request->validated();
        $expenseLedger = ExpenseLedger::create($validated);
        
        if($expenseLedger['deduct']){
            $updateExpense = $this -> updateExpense($expenseLedger);    
        }
        // dd('Deduct from Account'. $expenseLedger['payment_method']);
        
        
        $expenseLedger = $this -> returnRedirection();
        $expenseLedgers = $expenseLedger[0];
        $search = $expenseLedger[1];
        return view(
            'app.expense_ledgers.index',
            compact('expenseLedgers', 'search')
        );
        // return redirect()
        //     ->route('expense-ledgers.edit', $expenseLedgers)
        //     ->withSuccess(__('crud.common.created'));
    }

    public function updateExpense($expenseLedger)
    {
        $transLastRecord = Transaction::query()->latest('id')->first();
       
        $transLastRecord = $transLastRecord->toArray();
        unset($transLastRecord['created_at']);
        unset($transLastRecord['upated_at']);
        unset($transLastRecord['sales_ledger_id']);
        if($expenseLedger['payment_method'] === 'cash'){
         $transLastRecord['cash_in_hand'] = $transLastRecord['cash_in_hand'] - $expenseLedger['purchase_price'];
        }
        else{
         $transLastRecord['bank_balance'] = $transLastRecord['bank_balance'] - $expenseLedger['purchase_price'];
        }
        $transLastRecord['expense_ledger_id'] = $expenseLedger['id'];
        $transLastRecord['reason'] = "Expense of: ". formatCurrency($expenseLedger['purchase_price'])." for " .$expenseLedger['name'];
        $transaction = Transaction::create($transLastRecord);
        
    }
    public function returnRedirection(){
        $search ='';
        $expenseLedgers = ExpenseLedger::search($search)
        ->latest()
        ->paginate(25)
        ->withQueryString();
        return [$expenseLedgers, $search];
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, ExpenseLedger $expenseLedger): View
    {
        $this->authorize('view', $expenseLedger);

        return view('app.expense_ledgers.show', compact('expenseLedger'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, ExpenseLedger $expenseLedger): View
    {
        $this->authorize('update', $expenseLedger);

        return view('app.expense_ledgers.edit', compact('expenseLedger'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(
        ExpenseLedgerUpdateRequest $request,
        ExpenseLedger $expenseLedger
    ): View {
        $this->authorize('update', $expenseLedger);

        $validated = $request->validated();

        $expenseLedger->update($validated);
        if($expenseLedger['deduct']){
            $updateExpense = $this -> updateExpense($expenseLedger);    
        }
        $expenseLedger = $this -> returnRedirection();
        $expenseLedgers = $expenseLedger[0];
        $search = $expenseLedger[1];
        return view(
            'app.expense_ledgers.index',
            compact('expenseLedgers', 'search')
        );
        // $updateExpense = $this -> updateExpense($expenseLedger);
        // $expenseLedgers = $updateExpense[0];
        // $search = $updateExpense[1];
        // return view(
        //     'app.expense_ledgers.index',
        //     compact('expenseLedgers', 'search')
        // );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(
        Request $request,
        ExpenseLedger $expenseLedger
    ): RedirectResponse {
        $this->authorize('delete', $expenseLedger);

        $expenseLedger->delete();

        return redirect()
            ->route('expense-ledgers.index')
            ->withSuccess(__('crud.common.removed'));
    }
}
