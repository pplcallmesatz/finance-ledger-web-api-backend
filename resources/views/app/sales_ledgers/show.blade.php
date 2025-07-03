<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            @lang('crud.sales_ledgers.show_title')
        </h2>
    </x-slot>

    <div class="py-12">
    
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <x-partials.card>
                <x-slot name="title">
                    <a href="{{ route('sales-ledgers.index') }}" class="mr-4"
                        ><i class="mr-1 icon ion-md-arrow-back"></i
                    ></a>
                    
                    @lang('crud.sales_ledgers.show_title')
                </x-slot>
                
                <div class="mt-4 px-4">
                <div class="flex w-full">
                    <div class="w-1/2">
                    <span class="text-sm">Date</span>    
                    <h4 class="text-lg font-bold mb-3">{{ $salesLedger->sales_date ?? '-' }} </h4>
                    </div>
                    <div class="w-1/2">
                        
                        <span class="text-sm">Invoice No</span>    
                        <p class="font-medium text-gray-700 text-lg">{{ $salesLedger->invoice_number ?? '-' }}</p>
                    </div>
                </div>
                    <div class="mb-4">
                    <span class="text-sm">Name</span>    
                    <h5 class="font-medium text-gray-700 text-lg">
                         {{ optional($salesLedger->user)->name ?? '-'}}
                        </h5>
                        <span
                            ></span
                        >
                    </div>
                    
                    <!-- <div class="mb-4">
                        <h5 class="font-medium text-gray-700">
                            @lang('crud.sales_ledgers.inputs.total_product_price')
                        </h5>
                        <span
                            >{{ $salesLedger->total_product_price ?? '-'
                            }}</span
                        >
                    </div>
                    <div class="mb-4">
                        <h5 class="font-medium text-gray-700">
                            @lang('crud.sales_ledgers.inputs.selling_product_price')
                        </h5>
                        <span
                            >{{ $salesLedger->selling_product_price ?? '-'
                            }}</span
                        >
                    </div> -->
                    <div class="flex flex-wrap">
                        <div class="mb-4 w-1/2">
                        <span class="text-sm">@lang('crud.sales_ledgers.inputs.payment_status')</span>    
                            <h5 class="font-medium text-gray-700 text-lg">
                                
                        
                            {{ $salesLedger->payment_status ?? '-' }}</h5>
                        </div>

                        <div class="mb-4 w-1/2">
                        <span class="text-sm"> @lang('crud.sales_ledgers.inputs.payment_method')</span> 
                            <h5 class="font-medium text-gray-700 text-lg">
                               
                            
                               
                            <span>{{ $salesLedger->payment_method ?? '-' }}</span></h5>
                        </div>
                    </div>
                    <div class="flex flex-wrap">
                        <div class="mb-8 w-1/2">
                        <span class="text-sm"> @lang('crud.sales_ledgers.inputs.remarks')</span> 
                            <p>{{ $salesLedger->remarks ?? '-' }}</p>
                        </div>
                        <div class="mb-8 w-1/2">
                        <span class="text-sm">@lang('crud.sales_ledgers.inputs.company_address')</span> 
                            <p>{{ $salesLedger->company_address ?? '-' }}</p>
                        </div>
                    </div>

                    <div class="flex flex-wrap">
                    @if($salesLedger->payment_status  == 'pending')
                        <div class="w-1/2">
                            <div>
                            <span class="text-sm"> Send Pending Remainder</span> </div>
                                <a class="rounded-md bg-green-50 mx-2 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20" href="{Your Whatsapp Link}" target="_blank">
                                                    Eng
                                                </a> 
                                                

                                <a class="rounded-md bg-green-50 mx-2 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20" href="{Your Whatsapp Link}" target="_blank">
                                            Tam    
                                            </a> 
                            </div>
                        @endif
                        <div class="w-1/2">
                            <div> <span class="text-sm"> Send Purchase Greetings</span> </div> 
                                <a class="rounded-md bg-green-50 mx-2 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20" href="{Your Whatsapp Link}" target="_blank">Eng </a> 
                        </div>
                    </div>

                </div>
                <div class="mb-4 mt-12">
                            <h1 class="text-xl font-bold mb-4">Product List</h1>
<style>
    table td,table th{
        text-align: left;
    }
    </style>
    <div class="overflow-auto	">
<table class="w-full">
<thead class=" bg-slate-400">
    <tr>
        <th class="px-2 py-3 whitespace-nowrap	">Product Name</th>
        <th class="px-2 py-3 whitespace-nowrap text-center">Batch Code</th>
        <th class="px-2 py-3 whitespace-nowrap text-center">Product Price</th>
        <th class="px-2 py-3 whitespace-nowrap text-center">Selling Price</th>
        <th class="px-2 py-3 whitespace-nowrap text-center">Customer Price</th>
        <th class="px-2 py-3 whitespace-nowrap text-center">Quantity</th>
        <th class="px-2 py-3 whitespace-nowrap text-center">Total Selling Price</th>
        <th class="px-2 py-3 whitespace-nowrap	 text-right">Billed to Customer</th>
    </tr>
</thead>
<tbody>
    
    @foreach ($salesLedger->products as $product)
    <tr>
        <td class="px-2 py-3">
            
        @if(isset($product->pivot->product_id))
        <a class="text-blue" target="_balnk" href="{{ route('products.show', ['id' => $product->pivot->product_id]) }}">
        {{$product->pivot->product_name}} 

        </a>
        @else
            N/A
        @endif
        
       </td>
        <td class="px-2 py-3">
            
        @if(isset($productMasters[$product->pivot->product_master_id]))
        <a class="text-blue" target="_balnk" href="{{ route('product-masters.show', ['id' => $product->pivot->product_master_id]) }}">
            {{ $productMasters[$product->pivot->product_master_id] }}
        </a>
    @else
        N/A
    @endif
        
</td>
        <td class="px-2 py-3 text-center">{{ formatCurrency($product->pivot->product_price) }}</td>
        <td class="px-2 py-3 text-center"> {{ formatCurrency($product->pivot->selling_price) }}</td>
        <td class="px-2 py-3 text-center"> {{ formatCurrency($product->pivot->customer_price) }}</td>
        <td class="px-2 py-3 text-center"> {{ $product->pivot->quantity }}</td>
        <td class="px-2 py-3 text-center"> {{ formatCurrency($product->pivot->quantity * $product->pivot->selling_price)}} </td>
        <td class="px-2 py-3 text-right"> {{ formatCurrency($product->pivot->quantity * $product->pivot->customer_price)}} </td>
    </tr>
    @endforeach
    <tr class="border-t border-slate-200	">
        <td class="px-2 py-3 text-center font-bold	text-xl" colspan="5">Total</td>
        <td class="px-2 py-3 text-center"> {{ formatCurrency($salesLedger->total_product_price) ?? '-' }}</td>
        <td class="px-2 py-3 text-center"> {{ formatCurrency($salesLedger->selling_product_price) ?? '-' }}</td>
        <td class="px-2 py-3 font-bold	text-xl text-right bg-green-300	"> {{ formatCurrency($salesLedger->total_customer_price) ?? '-' }}</td>
    </tr>
</tbody>
</table>
       
</div>
</div>

                <div class="mt-10">
                    <a href="{{ route('sales-ledgers.index') }}" class="button">
                        <i class="mr-1 icon ion-md-return-left"></i>
                        @lang('crud.common.back')
                    </a>

                    @can('create', App\Models\SalesLedger::class)
                    <a
                        href="{{ route('sales-ledgers.create') }}"
                        class="button"
                    >
                        <i class="mr-1 icon ion-md-add"></i>
                        @lang('crud.common.create')
                    </a>
                    @endcan

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
                                                    class="mr-1 icon ion-md-create"
                                                ></i> Edit
                                            </button>
                                        </a>
                                        @endcan
                </div>
            </x-partials.card>
        </div>
    </div>
</x-app-layout>

