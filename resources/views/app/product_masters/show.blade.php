<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            @lang('crud.product_masters.show_title')
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <x-partials.card>
                <x-slot name="title">
                    <a href="{{ route('product-masters.index') }}" class="mr-4"
                        ><i class="mr-1 icon ion-md-arrow-back"></i
                    ></a>
                    @lang('crud.product_masters.show_title')
                </x-slot>

                

                <div class="mt-4 px-4 flex flex-wrap">
                <!-- <div class="mt-8 mb-4 w-1/2">
                        <span class="text-sm">Category</span>    
                        <h5 class="font-medium text-gray-700 text-lg">
                        {{ optional($productMaster->categoryMaster)->name
                            ?? '-' }}
                        </h5>
                    </div> -->

                    <div class="mt-8 mb-4 w-1/4">
                        <span class="text-sm">Product @lang('crud.product_masters.inputs.name')</span>    
                        <h5 class="font-medium text-gray-700 text-lg ">
                            {{ $productMaster->name ?? '-' }} <span class="rounded-md bg-green-50 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20">{{ optional($productMaster->categoryMaster)->name
                            ?? '-' }}</span>
                        </h5>
                    </div>

                    <div class="mt-8 mb-4 w-1/4">
                        <span class="text-sm">Product @lang('crud.product_masters.inputs.invoice_number')</span>    
                        <h5 class="font-medium text-gray-700 text-lg">
                            {{ $productMaster->invoice_number ?? '-' }} 
                        </h5>
                    </div>

                    <div class="mt-8 mb-4 w-1/4">
                        <span class="text-sm">Batch Number</span>    
                        <h5 class="font-medium text-gray-700 text-lg">
                        <span class="rounded-md bg-green-50 px-2 py-1 text-green-700 ring-1 ring-inset ring-green-600/20">{{ $productMaster->batch_number ?? '-' }} </span>
                        </h5>
                    </div>
                    
                <div class="w-full flex flex-wrap">

                    <div class="mt-8 mb-4 w-1/4">
                        <span class="text-sm">Purchase Date</span>    
                        <h5 class="font-medium text-gray-700 text-lg">
                        {{ \Carbon\Carbon::parse($productMaster->purchase_date)->format('d M Y') }}
                        </h5>
                    </div>

                    <div class="mt-8 mb-4 w-1/4">
                        <span class="text-sm">Manufacturing Date</span>    
                        <h5 class="font-medium text-gray-700 text-lg">
                        {{ \Carbon\Carbon::parse($productMaster->manufacturing_date)->format('d M Y') }}
                            
                        </h5>
                    </div>

                    <div class="mt-8 mb-4 w-1/4">
                        <span class="text-sm">Expire Date</span>    
                        <h5 class="font-medium text-gray-700 text-lg">
                        {{ \Carbon\Carbon::parse($productMaster->expire_date)->format('d M Y') }}
                            
                        </h5>
                    </div>

                </div>
                <div class="w-full flex flex-wrap">
                
                <div class="mt-8 mb-4 w-1/4">
                        <span class="text-sm">@lang('crud.product_masters.inputs.quantity_purchased')</span>    
                        <h5 class="font-medium text-gray-700 text-lg">
                        {{ $productMaster->quantity_purchased ?? '-'}}
                        </h5>
                    </div>

                <div class="mt-8 mb-4 w-1/4">
                        <span class="text-sm">Total Purchase Price</span>    
                        <h5 class="font-medium text-gray-700 text-lg">
                        {{ formatCurrency($productMaster->purchase_price )?? '-' }} 
                        </h5>
                    </div>

                    <div class="mt-8 mb-4 w-1/4">
                        <span class="text-sm">@lang('crud.product_masters.inputs.transportation_cost')</span>    
                        <h5 class="font-medium text-gray-700 text-lg">
                        {{ formatCurrency($productMaster->transportation_cost) ?? '-'
                            }}
                        </h5>
                    </div>

                    <div class="mt-8 mb-4 w-1/4">
                        <span class="text-sm">Per Unit cost</span>    
                        <h5 class="font-medium text-gray-700 text-lg">
                        @php
                            // Retrieve the values from the $productMaster object
                            $purchasePrice = $productMaster->purchase_price ?? 0;
                            $transportationCost = $productMaster->transportation_cost ?? 0;
                            $quantityPurchased = $productMaster->quantity_purchased ?? 0;
                            $totalCost = 0;
                                // Check to avoid division by zero
                                if ($quantityPurchased > 0) {
                                    $totalCost = $purchasePrice + $transportationCost;
                                    $result = $totalCost / $quantityPurchased;
                                } else {
                                    $result = '-'; // Or any other default value or message
                                }
                                
                                $result = ceil($result * 100) / 100;
                            @endphp

                                    <!-- Display the result -->
                                   <span class="rounded-md bg-green-50 px-2 py-1 font-medium text-green-700 ring-1 ring-inset ring-green-600/20"> {{ formatCurrency($result) }}</span> / <span class="text-xs">{{formatCurrency($totalCost)}} (Total Cost)</span>
                        </h5>
                    </div>
                    


                    

                            </div>
                    

                    

                   
                    
                    <div class="mt-8 mb-4 w-1/4">
                        <h5 class="font-medium text-gray-700">
                            @lang('crud.product_masters.inputs.vendor')
                        </h5>
                        <span>{{ $productMaster->vendor ?? '-' }}</span>
                    </div>
                </div>

                
                <div class="mt-10">
                    <a
                        href="{{ route('product-masters.index') }}"
                        class="button"
                    >
                        <i class="mr-1 icon ion-md-return-left"></i>
                        @lang('crud.common.back')
                    </a>

                    @can('create', App\Models\ProductMaster::class)
                    <a
                        href="{{ route('product-masters.create') }}"
                        class="button"
                    >
                        <i class="mr-1 icon ion-md-add"></i>
                        @lang('crud.common.create')
                    </a>
                    @endcan
                </div>
            </x-partials.card>
        </div>
    </div>
</x-app-layout>
