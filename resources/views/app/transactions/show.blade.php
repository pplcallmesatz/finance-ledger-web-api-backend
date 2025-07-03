<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            @lang('crud.transactions.show_title')
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <x-partials.card>
                <x-slot name="title">
                    <a href="{{ route('transactions.index') }}" class="mr-4"
                        ><i class="mr-1 icon ion-md-arrow-back"></i
                    ></a>
                    @lang('crud.transactions.show_title')
                </x-slot>

                <div class="mt-4 px-4">
                    <div class="mb-4">
                        <h5 class="font-medium text-gray-700">
                            @lang('crud.transactions.inputs.bank_balance')
                        </h5>
                        <span>{{ formatCurrency($transaction->bank_balance) ?? '-' }}</span>
                    </div>
                    <div class="mb-4">
                        <h5 class="font-medium text-gray-700">
                            @lang('crud.transactions.inputs.cash_in_hand')
                        </h5>
                        <span>{{ formatCurrency($transaction->cash_in_hand) ?? '-' }}</span>
                    </div>
                    <div class="mb-4">
                        <h5 class="font-medium text-gray-700">
                            @lang('crud.transactions.inputs.reason')
                        </h5>
                        <span>{{ $transaction->reason ?? '-' }}</span>
                    </div>
                    <div class="mb-4 button">
                        @if(!is_null($transaction->expense_ledger_id))
                            <a href="{{ route('expense-ledgers.show', $transaction->expense_ledger_id) }}">See the Expense details</a>
                        @else
                            <a href="{{ route('sales-ledgers.show', $transaction->sales_ledger_id) }}">See the Sales details</a>
                        @endif
                    </div>
                </div>
                
                
                <div class="mt-10">
                    <a href="{{ route('transactions.index') }}" class="button">
                        <i class="mr-1 icon ion-md-return-left"></i>
                        @lang('crud.common.back')
                    </a>

                    @can('create', App\Models\Transaction::class)
                    <a href="{{ route('transactions.create') }}" class="button">
                        <i class="mr-1 icon ion-md-add"></i>
                        @lang('crud.common.create')
                    </a>
                    @endcan
                </div>
            </x-partials.card>
        </div>
    </div>
</x-app-layout>
