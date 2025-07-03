<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            @lang('crud.products.show_title')
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <x-partials.card>
                <x-slot name="title">
                    <a href="{{ route('products.index') }}" class="mr-4"
                        ><i class="mr-1 icon ion-md-arrow-back"></i
                    ></a>
                    @lang('crud.products.show_title')
                </x-slot>

                <div class="mt-4 mb-4 px-4">

                <div class="mt-8 mb-4 w-1/4">
                        <span class="text-sm">Product @lang('crud.product_masters.inputs.name')</span>    
                        <h5 class="font-medium text-gray-700 text-lg ">
                            {{ $product->name ?? '-' }} <span class="rounded-md bg-green-50 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20">{{ optional($product->categoryMaster)->name
                            ?? '-' }}</span>
                        </h5>
                    </div>

                    <div class="mt-8 mb-4 w-1/4">
                        <span class="text-sm">Product Per Unit</span>    
                        <h5 class="font-medium text-gray-700 text-lg ">
                            {{ $product->units ?? '-' }} <span class="text-sm">/ kg</span>
                        </h5>
                    </div>

                    
                    <div class="flex flex-wrap">
                        <div class="mt-8 mb-4 w-1/4">
                            <span class="text-sm">Per unit cost</span>    
                            <h5 class="font-medium text-gray-700 text-lg">
                                {{ formatCurrency($product->purchase_price) ?? '-' }}
                            </h5>
                        </div>


                        <div class="mt-8 mb-4 w-1/4">
                            <span class="text-sm">Product Price</span>    
                            <h5 class="font-medium text-gray-700 text-lg">
                                {{ formatCurrency($product->packing_price) ?? '-' }}
                            </h5>
                        </div>

                        <div class="mt-8 mb-4 w-1/4">
                            <span class="text-sm">Packing Price</span>    
                            <h5 class="font-medium text-gray-700 text-lg">
                                {{ formatCurrency($product->purchase_price + $product->packing_price) ?? '-' }}
                            </h5>
                        </div>


                        


                        <div class="mt-8 mb-4 w-1/4">
                            <span class="text-sm">Selling Price</span>    
                            <h5 class="font-medium text-gray-700 text-lg">
                            <span class="rounded-md bg-green-50 px-2 py-1 text-green-700 ring-1 ring-inset ring-green-600/20"> {{ formatCurrency($product->selling_price) ?? '-' }}</span>
                            </h5>
                        </div>

                    </div>
                    
                    
                    
                    
                   
                    
                    
                    <div class="mt-8 mb-8">
                        <h5 class="font-medium text-gray-700">
                            @lang('crud.products.inputs.description')
                        </h5>
                        <span>{{ $product->description ?? '-' }}</span>
                    </div>
                </div>



                <div class=" border-slate-300">
                    <div class="mt-10 pt-10 border-slate-300">
                        <h4 class="text-lg font-semibold">Average Purchase Timeline by User:</h4>
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Average Days</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Purchase Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Predicted Next Purchase Date</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($userPurchaseData as $userId => $data)
                                    @php
                                        $isOverdue = $data['next_purchase_date'] && $data['next_purchase_date']->isPast();
                                    @endphp
                                    <tr class="bb {{ $isOverdue ? 'bg-red-100' : '' }}">
                                        <td class="px-6 py-4 whitespace-nowrap border border-slate-200">
                                            <a href="{{ route('users.show', $userId) }}" target="_blank">
                                                {{ $data['name'] }}
                                            </a>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap border border-slate-200">{{ $data['average_days'] }} days</td>
                                        <td class="px-6 py-4 whitespace-nowrap border border-slate-200">{{ $data['last_purchase_date'] ? $data['last_purchase_date']->toDateString() : 'N/A' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap border border-slate-200">{{ $data['next_purchase_date'] ? $data['next_purchase_date']->toDateString() : 'N/A' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>


                

                <div class="mt-10">
                    <a href="{{ route('products.index') }}" class="button">
                        <i class="mr-1 icon ion-md-return-left"></i>
                        @lang('crud.common.back')
                    </a>

                    @can('create', App\Models\Product::class)
                    <a href="{{ route('products.create') }}" class="button">
                        <i class="mr-1 icon ion-md-add"></i>
                        @lang('crud.common.create')
                    </a>
                    @endcan
                </div>
            </x-partials.card>
        </div>
    </div>
</x-app-layout>
