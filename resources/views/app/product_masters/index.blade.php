<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            @lang('crud.product_masters.index_title')
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <x-partials.card>
                <x-slot name="title">
                    @lang('crud.product_masters.index_title')
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

                                    <x-inputs.group class="w-full">
                                    <x-inputs.select
                                        name="category"                                        
                                        value="{{ $category ?? '' }}"
                                    >
                                    <option value="" {{ old('category', $category ?? '') === '' ? 'selected' : '' }}>Choose category</option>
                                        @foreach($categoryMasters as $value => $label)
                                        
                                        <option {{$category == $value? 'selected': $label}} value="{{ $value }}">{{ $label }}</option>
                                        @endforeach
                                    </x-inputs.select>
                                </x-inputs.group>

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
                            @can('create', App\Models\ProductMaster::class)
                            <a
                                href="{{ route('product-masters.create') }}"
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
                            <th>Action</th>
                            <th class="px-4 py-3 text-left">
                                    Date
                                </th>    
                            <th class="px-4 py-3 text-left">
                                    Category
                                </th>
                                <th class="px-4 py-3 text-left">
                                    Product Name
                                </th>
                                <th class="px-4 py-3 text-left">
                                    @lang('crud.product_masters.inputs.batch_number')
                                </th>
                                
                                <th class="px-4 py-3 text-left">
                                    @lang('crud.product_masters.inputs.invoice_number')
                                </th>
                                <th class="px-4 py-3 text-left">
                                    @lang('crud.product_masters.inputs.quantity_purchased')
                                </th>

                                <th class="px-4 py-3 text-left">
                                    Available Qty
                                </th>

                                <th class="px-4 py-3 text-left">
                                    Per Unit cost
                                </th>
                               
                            </tr>
                        </thead>
                        <tbody class="text-gray-600">
                            @forelse($productMasters as $productMaster)
                            <tr class="hover:bg-gray-50">
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
                                        @can('update', $productMaster)
                                        <a
                                            href="{{ route('product-masters.edit', $productMaster) }}"
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
                                        @endcan @can('view', $productMaster)
                                        <a
                                            href="{{ route('product-masters.show', $productMaster) }}"
                                            class="mr-1"
                                        >
                                            <button
                                                type="button"
                                                class="button"
                                            >
                                                <i class="icon ion-md-eye"></i>
                                            </button>
                                        </a>
                                        @endcan @can('delete', $productMaster)
                                        <form
                                            action="{{ route('product-masters.destroy', $productMaster) }}"
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
                                <td class="px-4 py-3 text-left">
                                {{ \Carbon\Carbon::parse($productMaster->created_at)->format('d M Y') }}
                                    
                                </td>
                                <td class="px-4 py-3 text-left">
                                    {{
                                    optional($productMaster->categoryMaster)->name
                                    ?? '-' }}
                                </td>
                                <td class="px-4 py-3 text-left">
                                    {{ $productMaster->name ?? '-' }}
                                </td>
                                <td class="px-4 py-3 text-left">
                                    {{ $productMaster->batch_number ?? '-' }}
                                </td>
                                
                               
                                <td class="px-4 py-3 text-left">
                                    {{ $productMaster->invoice_number ?? '-' }}
                                </td>
                                <td class="px-4 py-3 text-left">
                                    {{ $productMaster->quantity_purchased ?? '-'
                                    }}
                                </td>
                                <td class="px-4 py-3 text-left">
                                    {{ $productMaster->total_piece ?? '-'}}
                                </td>
                                

                                <td class="px-4 py-3 text-left">
                                    @php
                                        // Retrieve the values from the $productMaster object
                                        $purchasePrice = $productMaster->purchase_price ?? 0;
                                        $transportationCost = $productMaster->transportation_cost ?? 0;
                                        $quantityPurchased = $productMaster->quantity_purchased ?? 0;

                                        // Check to avoid division by zero
                                        if ($quantityPurchased > 0) {
                                            $result = ($purchasePrice + $transportationCost) / $quantityPurchased;
                                        } else {
                                            $result = '-'; // Or any other default value or message
                                        }
                                        $result = ceil($result * 100) / 100;
                                    @endphp

                                    <!-- Display the result -->
                                    <span class="rounded-md bg-green-50 px-2 py-1 font-medium text-green-700 ring-1 ring-inset ring-green-600/20"> {{ formatCurrency($result) }}</span>
                                </td>
                               
                            </tr>
                            @empty
                            <tr>
                                <td colspan="10">
                                    @lang('crud.common.no_items_found')
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="10">
                                    <div class="mt-10 px-4">
                                        {!! $productMasters->render() !!}
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
