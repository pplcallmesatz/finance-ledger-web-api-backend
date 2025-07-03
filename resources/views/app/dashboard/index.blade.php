<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight mt-7">
           Dashboard - {{$userName}}
        </h2>
    </x-slot>


    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <x-partials.card>
            
            <div class="mb-10 pt-10 border-slate-300">
                <h3 class="text-lg font-semibold">Product Availability</h3>
                <div class="flex flex-wrap mt-4 mb-4">
                    @foreach($categoryMasters as $category)
                        <div class="py-2 px-2 w-full md:w-1/2 lg:w-1/3">
                            <div class="rounded-md border px-3 py-2 flex justify-between {{ $category->total_available_quantity < 1 ? 'bg-red-100' : 'bg-white' }}">
                            <h4 class="text-lg font-semibold">{{ $category->name }}</h4>
                            <div class="px-2 py-1 rounded-lg text-xs {{ $category->total_available_quantity < 1 ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' }} font-semibold flex items-center justify-center">{{ $category->total_available_quantity }}</div>
</div>
                        </div>
                    @endforeach
                </div>
            </div>


            <div class="border-slate-300">
                <h3 class="text-lg	font-semibold	">Status</h3>
                <div class="flex flex-wrap mt-4 mb-4">
                    <div class="py-3 px-8 w-full md:w-1/2 lg:w-1/4 text-center bg-green-100">
                        <h4 class="text-2xl	text-green-700 font-semibold">{{formatCurrency($totalPending)}}</h4>
                        <div class="text-green-700">Total Pendings</div>
                    </div>
                    <div class="py-3 px-8 w-full md:w-1/2 lg:w-1/4 text-center bg-yellow-100	">
                        <h4 class="text-2xl	text-yellow-700 font-semibold">{{formatCurrency($totalProfit)}}</h4>
                        <div class="text-yellow-700">Total Profit</div>
                     </div>
                    <div class="py-3 px-8 w-full md:w-1/2 lg:w-1/4 text-center bg-sky-100">
                        <h4 class="text-2xl	text-sky-700 font-semibold">{{formatCurrency($totalSales)}}</h4>
                        <div class="text-sky-700">Total Sales</div>
                    </div>
                    <div class="py-3 px-8 w-full md:w-1/2 lg:w-1/4 text-center bg-fuchsia-100	">
                        <h4 class="text-2xl	text-fuchsia-700 font-semibold">{{formatCurrency($totalExpenses)}}</h4>
                        <div class="text-fuchsia-700">Total Expense</div>
                     </div>
                </div>  
            
            </div>




            <div class="mt-8 pt-8 border-slate-300">
                <h3 class="text-lg	font-semibold	">Cash Flow</h3>
                <div class="flex flex-wrap mt-4 mb-4">
                    <div class="py-3 px-8 w-full md:w-1/2 text-center bg-green-100">
                        <h4 class="text-2xl	text-green-700 font-semibold">{{ formatCurrency(optional($transactions)->bank_balance)??'0' }}</h4>
                        <div class="text-green-700">Bank Balance</div>
                    </div>
                    <div class="py-3 px-8 w-full md:w-1/2 text-center bg-yellow-100	">
                        <h4 class="text-2xl	text-yellow-700 font-semibold">{{ formatCurrency(optional($transactions)->cash_in_hand)?? '0' }}</h4>
                        <div class="text-yellow-700">Cash Balance</div>
                     </div>
                </div>    
            </div>


        <div class="mt-10 pt-10	border-slate-300	">
            <h3 class="text-lg	font-semibold	">Profit / Loss Product Wise</h3>
            <div class="flex flex-wrap mt-4 mb-4">
                <div class="py-3 px-8 w-full md:w-1/3 text-center bg-green-100">
                    <h4 class="text-2xl	text-green-700 font-semibold">{{ formatCurrency($overallPrice['CustomerPrice'] - $overallPrice['ProductPrice']) }}</h4>
                    <div class="text-green-700">Total Profit</div>
                </div>
                <div class="py-3 px-8 w-full md:w-1/3 text-center bg-yellow-100	">
                    <h4 class="text-2xl	text-yellow-700 font-semibold">{{formatCurrency($overallPrice['CustomerPrice'])}}</h4>
                    <div class="text-yellow-700">Total Customer Price</div>
                </div>
                <div class="py-3 px-8 w-full md:w-1/3 text-center bg-sky-100	">
                    <h4 class="text-2xl	text-sky-700 font-semibold">{{formatCurrency($overallPrice['ProductPrice'])}}</h4>
                    <div class="text-sky-700">Total Product Price</div>
                </div>
            </div>
            <div class="flex flex-wrap mt-4 mb-4">
            @foreach($groupedProductSums as $productId => $products)
                @foreach($products as $product)
                    <div class="py-2 px-2 w-full md:w-1/2 lg:w-1/3">
                            <div class="rounded-md	border px-3 py-2">
                            <span class="px-2 py-1 rounded-lg text-xs bg-green-100 text-green-700 ">{{ $product->category_name }}</span>
                            <h4 class="mt-2 text-lg font-semibold">{{ $product->product_name }}</h4>
                            <div class="flex items-center	">
                                <span class="w-1/2 py-2">Total Prod. Price	</span> <span class=" w-1/2 px-2 py-1 font-semibold">{{ formatCurrency($product->total_product_price) }}</span>
                            </div>
                            <div class="flex items-center	">
                                <span class="w-1/2 py-2">Total Cust. Price</span> <span class="w-1/2 p-2 font-semibold ">{{ formatCurrency($product->total_customer_price) }}</span>
                            </div>
                            <div class="flex items-center	">
                                <span class="w-1/2 py-2">Total profit</span> <span class="p-2 font-semibold {{ ($product->total_customer_price - $product->total_product_price) > 0 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}  rounded-md ">{{ formatCurrency($product->total_customer_price - $product->total_product_price) }}</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endforeach
                
            </div>
        </div>
            </x-partials.card>
                  
        </div>
    </div>
</x-app-layout>

