<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            @lang('crud.expense_ledgers.index_title') 
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <x-partials.card>
                <x-slot name="title">
                    @lang('crud.expense_ledgers.index_title')
                </x-slot>

                <div class="mb-5 mt-4">
                    <div class="flex flex-wrap justify-between">
                        <div class="md:w-1/2">
                            <form>
                                <div class="flex items-center w-full">
                                    <x-inputs.text
                                        name="search"
                                        value="{{ $search ?? '' }}"
                                        placeholder="{{ __('crud.common.search') }}"
                                        autocomplete="off"
                                    ></x-inputs.text>

                                    <div class="ml-1">
                                        <button
                                            type="submit"
                                            class="button button-primary"
                                        >
                                            <i class="icon ion-md-search"></i>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="md:w-1/2 text-right">
                            @can('create', App\Models\ExpenseLedger::class)
                            <a
                                href="{{ route('expense-ledgers.create') }}"
                                class="button button-primary"
                            >
                                <i class="mr-1 icon ion-md-add"></i>
                                @lang('crud.common.create')
                            </a>
                            @endcan
                        </div>
                    </div>
                </div>

                <div class="block w-full overflow-auto scrolling-touch">
                    <table class="w-full max-w-full mb-4 bg-transparent">
                        <thead class="text-gray-700">
                            <tr>
                                <th>Created At</th>                                
                                <th class="px-4 py-3 text-left">
                                    @lang('crud.expense_ledgers.inputs.name')
                                </th>
                                <th class="px-4 py-3 text-left">
                                    @lang('crud.expense_ledgers.inputs.invoice_number')
                                </th>
                                <th class="px-4 py-3 text-right">
                                    @lang('crud.expense_ledgers.inputs.purchase_price')
                                </th>
                                <th class="px-4 py-3 text-left">
                                    @lang('crud.expense_ledgers.inputs.seller')
                                </th>
                                <th class="px-4 py-3 text-left">
                                    @lang('crud.expense_ledgers.inputs.purchase_date')
                                </th>
                                <th class="px-4 py-3 text-left">
                                    @lang('crud.expense_ledgers.inputs.payment_method')
                                </th>
                                <th>Updated At</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-600">
                            @forelse($expenseLedgers as $expenseLedger)
                            <tr class="hover:bg-gray-50">
                                <td>{{$expenseLedger -> created_at ?? '-'}}</td>
                                <td class="px-4 py-3 text-left">
                                    {{ $expenseLedger->name ?? '-' }}
                                </td>
                                <td class="px-4 py-3 text-left">
                                    {{ $expenseLedger->invoice_number ?? '-' }}
                                </td>
                                <td class="px-4 py-3 text-right">
                                    {{ $expenseLedger->purchase_price ?? '-' }}
                                </td>
                                <td class="px-4 py-3 text-left">
                                    {{ $expenseLedger->seller ?? '-' }}
                                </td>
                                <td class="px-4 py-3 text-left">
                                {{ \Carbon\Carbon::parse($expenseLedger->purchase_date)->format('j F, Y') }}
                                </td>
                                <td class="px-4 py-3 text-left">
                                    {{ $expenseLedger->payment_method ?? '-' }}
                                </td>
                                <td>{{$expenseLedger -> updated_at ?? '-'}}</td>
                                <td
                                    class="px-4 py-3 text-center"
                                    style="width: 134px;"
                                >
                                    <div
                                        role="group"
                                        aria-label="Row Actions"
                                        class="
                                            relative
                                            inline-flex
                                            align-middle
                                        "
                                    >
                                        @can('update', $expenseLedger)
                                        <a
                                            href="{{ route('expense-ledgers.edit', $expenseLedger) }}"
                                            class="mr-1"
                                        >
                                            <button
                                                type="button"
                                                class="button"
                                            >
                                                <i
                                                    class="icon ion-md-create"
                                                ></i>
                                            </button>
                                        </a>
                                        @endcan @can('view', $expenseLedger)
                                        <a
                                            href="{{ route('expense-ledgers.show', $expenseLedger) }}"
                                            class="mr-1"
                                        >
                                            <button
                                                type="button"
                                                class="button"
                                            >
                                                <i class="icon ion-md-eye"></i>
                                            </button>
                                        </a>
                                        @endcan @can('delete', $expenseLedger)
                                        <form
                                            action="{{ route('expense-ledgers.destroy', $expenseLedger) }}"
                                            method="POST"
                                            onsubmit="return confirm('{{ __('crud.common.are_you_sure') }}')"
                                        >
                                            @csrf @method('DELETE')
                                            <button
                                                type="submit"
                                                class="button"
                                            >
                                                <i
                                                    class="
                                                        icon
                                                        ion-md-trash
                                                        text-red-600
                                                    "
                                                ></i>
                                            </button>
                                        </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8">
                                    @lang('crud.common.no_items_found')
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="8">
                                    <div class="mt-10 px-4">
                                        {!! $expenseLedgers->render() !!}
                                    </div>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </x-partials.card>
        </div>
    </div>
</x-app-layout>
