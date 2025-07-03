<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            @lang('crud.users.show_title')
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <x-partials.card>
                <x-slot name="title">
                    <a href="{{ route('users.index') }}" class="mr-4"
                        ><i class="mr-1 icon ion-md-arrow-back"></i
                    ></a>
                    @lang('crud.users.show_title')
                </x-slot>

                <div class="mt-4 px-4">


                    <div class="mt-8 mb-4 md:w-1/4">
                        <span class="text-sm">@lang('crud.users.inputs.name')</span>    
                        <h5 class="font-medium text-gray-700 text-lg">
                            {{ $user->name ?? '-' }}
                        </h5>
                    </div>

                    <div class="mt-8 mb-4 md:w-1/4">
                        <span class="text-sm">@lang('crud.users.inputs.email')</span>    
                        <h5 class="font-medium text-gray-700 text-lg">
                        {{ $user->email ?? '-' }}
                        </h5>
                    </div>

                    <div class="mt-8 mb-4 md:w-1/4">
                        <span class="text-sm">@lang('crud.users.inputs.phone')</span>    
                        <h5 class="font-medium text-gray-700 text-lg">
                        {{ $user->phone ?? '-' }}
                        </h5>
                    </div>


                    <div class="mt-8 mb-4 md:w-1/2">
                        <span class="text-sm">@lang('crud.users.inputs.remarks')</span>    
                        <h5 class="font-medium text-gray-700 text-lg">
                        {{ $user->remarks ?? '-' }}
                        </h5>
                    </div>
                    <div class="w-full flex flex-wrap">
                        <div class="mt-8 mb-4 md:w-1/2">
                            <span class="text-sm">Total Purchased</span>    
                            <h5 class="font-medium text-gray-700 text-lg">
                            <span class="rounded-md  bg-green-50 text-green-700 px-2 py1">{{ formatCurrency($totalPurchased) ?? '-' }}</span>
                            </h5>
                        </div>
                        <div class="mt-8 mb-4 md:w-1/2">
                            <span class="text-sm">Total Pending</span>    
                            <h5 class="font-medium  text-lg 	">
                            <span class="rounded-md text-red-700 bg-red-200 px-2 py1">{{ formatCurrency($totalPendingPayment) ?? '-' }}</span>
                            </h5>
                        </div>
                    </div>
                    
                    
                <br/>
                <br/>

                
                

                    @if(!$getPurchasePendingData->isEmpty())
                    
                    <h2 class="text-xl font-bold mb-4">Pending Payments</h2>
                        <table class="table-auto w-full">
                            <thead class="bg-slate-300">
                                <tr>
                                    <!-- <th class="px-4 py-2">ID</th> -->
                                    <th class="px-4 py-2">Date</th>
                                    
                                    <th class="px-4 py-2 text-left">Payment Status</th>
                                    <th class="px-4 py-2 text-left">Payment Method</th>
                                    <th class="px-4 py-2 text-right">Total Price</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($getPurchasePendingData as $purchase)
                                    <tr>
                                        <!-- <td class="border px-4 py-2">{{ $purchase->id }}</td> -->
                                        <td class="border border-slate-200	 px-4 py-2">
                                    <a href="{{ route('sales-ledgers.show', $purchase->id) }}" >   {{ $purchase->sales_date }} </a></td>
                                        <td class="border border-slate-200	 px-4 py-2">{{ $purchase->payment_status }}</td>
                                        <td class="border border-slate-200	 px-4 py-2">{{ $purchase->payment_method }}</td>
                                        <td class="border border-slate-200	 px-4 py-2 text-right">{{ formatCurrency($purchase->total_customer_price) }}</td>
                                        
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                         <!-- Pagination links -->
                        <div class="mt-4">
                            {{ $getPurchasePendingData->links() }}
                        </div>
                    @endif

                
                
                <br/>
                
                <h2 class="text-xl font-bold mb-4">Purchases</h2>

                    @if($getPurchaseData->isEmpty())
                        <p>No purchases found for this user.</p>
                    @else
                        <table class="table-auto w-full">
                            <thead class="bg-slate-300">
                                <tr>
                                    <!-- <th class="px-4 py-2">ID</th> -->
                                    <th class="px-4 py-2">Date</th>
                                    
                                    <th class="px-4 py-2 text-left">Payment Status</th>
                                    <th class="px-4 py-2 text-left">Payment Method</th>
                                    <th class="px-4 py-2 text-right">Total Price</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($getPurchaseData as $purchase)
                                    <tr>
                                        <!-- <td class="border px-4 py-2">{{ $purchase->id }}</td> -->
                                        <td class="border border-slate-200	 px-4 py-2">
                                    <a href="{{ route('sales-ledgers.show', $purchase->id) }}" >   {{ $purchase->sales_date }} </a></td>
                                        <td class="border border-slate-200	 px-4 py-2">{{ $purchase->payment_status }}</td>
                                        <td class="border border-slate-200	 px-4 py-2">{{ $purchase->payment_method }}</td>
                                        <td class="border border-slate-200	 px-4 py-2 text-right">{{ formatCurrency($purchase->total_customer_price) }}</td>
                                        
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                         <!-- Pagination links -->
                        <div class="mt-4">
                            {{ $getPurchaseData->links() }}
                        </div>
                    @endif

                <br/>
                <br/>

                <div class="mt-10">
                    <a href="{{ route('users.index') }}" class="button">
                        <i class="mr-1 icon ion-md-return-left"></i>
                        @lang('crud.common.back')
                    </a>

                    
                    <a href="{{ route('users.create') }}" class="button">
                        <i class="mr-1 icon ion-md-add"></i>
                        @lang('crud.common.create')
                    </a>
                    
                </div>
            </x-partials.card>
        </div>
    </div>
</x-app-layout>
