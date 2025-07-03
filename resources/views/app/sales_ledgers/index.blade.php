<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            @lang('crud.sales_ledgers.index_title')
        </h2>
    </x-slot>
    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <x-partials.card>

                        <div class="border-slate-300">
                            <h3 class="text-lg	font-semibold	">Status</h3>
                            <div class="flex flex-wrap mt-4 mb-4">
                                <div class="py-3 px-8 w-full md:w-1/4 lg:w-1/4 text-center bg-green-100">
                                    <h4 class="text-2xl	text-green-700 font-semibold">{{formatCurrency($displayTotal['pendingTotalCustomerPrice'])}}</h4>
                                    <div class="text-green-700">Total Pendings</div>
                                </div>
                                <div class="py-3 px-8 w-full md:w-1/4 lg:w-1/4 text-center bg-fuchsia-100	">
                                    <h4 class="text-2xl	text-fuchsia-700 font-semibold">{{formatCurrency(($displayTotal['totalCustomerPrice']) - ($displayTotal['totalProductPrice']))}}</h4>
                                    <div class="text-fuchsia-700">Total Profit</div>
                                </div>
                                <div class="py-3 px-8 w-full md:w-1/4 lg:w-1/4 text-center bg-yellow-100	">
                                    <h4 class="text-2xl	text-yellow-700 font-semibold">{{formatCurrency($displayTotal['totalCustomerPrice'])}}</h4>
                                    <div class="text-yellow-700">Total Customer Price</div>
                                </div>
                                <div class="py-3 px-8 w-full md:w-1/4 lg:w-1/4 text-center bg-sky-100">
                                    <h4 class="text-2xl	text-sky-700 font-semibold">{{formatCurrency($displayTotal['totalProductPrice'])}}</h4>
                                    <div class="text-sky-700">Total Product Sales</div>
                                </div>
                                
                            </div>  
                        
                        </div>
            </x-partials.card>
        </div>
    </div>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <x-partials.card>
                <x-slot name="title">
                    @lang('crud.sales_ledgers.index_title')
                </x-slot>

                <div class="mb-5 mt-4">
                    <div class="flex md:flex-wrap lg:flex-nowrap justify-between">
                        <div class="w-full">
                            <form>
                                <div class="">
                                    <x-inputs.text
                                        name="search"
                                        value="{{ $search ?? '' }}"
                                        placeholder="{{ __('crud.common.search') }}"
                                        autocomplete="off"
                                        class="inline-block"
                                        style="width: auto !important"
                                    ></x-inputs.text>
                                    <x-inputs.select
                                        name="user_id"                                        
                                        value="{{ $user_id ?? '' }}"
                                        class="inline-block" 
                                        style="width: auto !important"
                                    >
                                    <option value="" {{ old('user', $user ?? '') === '' ? 'selected' : '' }}>Choose category</option>
                                        @foreach($userList as $value => $label)
                                        
                                        <option {{$user == $value? 'selected': $label}} value="{{ $value }}">{{ $label }}</option>
                                        @endforeach
                                    </x-inputs.select>
                                    <x-inputs.date
                                        name="start_date"
                                        value="{{ $startDate ?? '' }}"
                                        placeholder="Start Date"
                                        autocomplete="off"
                                        class="inline-block" 
                                        style="width: auto !important"
                                    ></x-inputs.date>
                                    <x-inputs.date
                                        name="end_date"
                                        value="{{ $endDate ?? '' }}"
                                        placeholder="End Date"
                                        autocomplete="off"
                                        class="inline-block" 
                                        style="width: auto !important"
                                    ></x-inputs.date>
                                    <div class="ml-1 inline-block">
                                        <button
                                            type="submit"
                                            class="button button-primary inline-block" 
                                            style="width: auto !important"
                                        >
                                            <i class="icon ion-md-search"></i>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="text-right">
                            @can('create', App\Models\SalesLedger::class)
                            <a
                                href="{{ route('sales-ledgers.create') }}"
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
                            <th class="px-4 py-3 text-left">
                                    @lang('crud.sales_ledgers.inputs.sales_date')
                                </th>
                            <th class="px-4 py-3 text-left">
                                    @lang('crud.sales_ledgers.inputs.invoice_number')
                                </th>
                                <th class="px-4 py-3 text-left">
                                    @lang('crud.sales_ledgers.inputs.user_id')
                                </th>
                                <th class="px-4 py-3 text-left">
                                    @lang('crud.sales_ledgers.inputs.payment_status')
                                </th>
                                
                                <th class="px-4 py-3 text-left">
                                    @lang('crud.sales_ledgers.inputs.payment_method')
                                </th>
                                <th class="px-4 py-3 text-right">
                                    @lang('crud.sales_ledgers.inputs.total_product_price')
                                </th>
                                <th class="px-4 py-3 text-right">
                                    @lang('crud.sales_ledgers.inputs.selling_product_price')
                                </th>

                                <th class="px-4 py-3 text-right">
                                    @lang('crud.sales_ledgers.inputs.total_customer_price')
                                </th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-600">
                            @forelse($salesLedgers as $salesLedger)
                            <tr class="hover:bg-gray-50">

                            <td class="px-4 py-3 text-left">
                                    {{ $salesLedger->sales_date ?? '-' }}
                                </td>
                            <td class="px-4 py-3 text-left">
                                    {{ $salesLedger->invoice_number ?? '-' }}
                                </td>
                                <td class="px-4 py-3 text-left">
                                    {{ optional($salesLedger->user)->name ?? '-'
                                    }}
                                </td>
                                <td class="px-4 py-3 text-left">
                                    {{ $salesLedger->payment_status ?? '-' }}
                                </td>
                               
                                <td class="px-4 py-3 text-left">
                                    {{ $salesLedger->payment_method ?? '-' }}
                                </td>
                                <td class="px-4 py-3 text-right">
                                    {{ formatCurrency($salesLedger->total_product_price) ?? '-'
                                    }}
                                </td>
                                <td class="px-4 py-3 text-right">
                                    {{ formatCurrency($salesLedger->selling_product_price) ??
                                    '-' }}
                                </td>
                                <td class="px-4 py-3 text-right">
                                    {{ formatCurrency($salesLedger->total_customer_price) ??
                                    '-' }}
                                </td>
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
                                        @can('update', $salesLedger)
                                        <a
                                            href="{{ route('sales-ledgers.edit', $salesLedger) }}"
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
                                        @endcan @can('view', $salesLedger)
                                        <a
                                            href="{{ route('sales-ledgers.show', $salesLedger) }}"
                                            class="mr-1"
                                        >
                                            <button
                                                type="button"
                                                class="button"
                                            >
                                                <i class="icon ion-md-eye"></i>
                                            </button>
                                        </a>
                                        @endcan @can('delete', $salesLedger)
                                        <form
                                            action="{{ route('sales-ledgers.destroy', $salesLedger) }}"
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
                                <td colspan="9">
                                    @lang('crud.common.no_items_found')
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="9">
                                    <div class="mt-10 px-4">
                                        {!! $salesLedgers->render() !!}
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
