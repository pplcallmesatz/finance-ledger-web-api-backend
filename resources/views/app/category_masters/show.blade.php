<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            @lang('crud.category_masters.show_title')
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <x-partials.card>
                <x-slot name="title">
                    <a href="{{ route('category-masters.index') }}" class="mr-4"
                        ><i class="mr-1 icon ion-md-arrow-back"></i
                    ></a>
                    @lang('crud.category_masters.show_title')
                </x-slot>

                <div class="mt-4 px-4">
                    <div class="mb-4">
                        <h5 class="font-medium text-gray-700">
                            @lang('crud.category_masters.inputs.name')
                        </h5>
                        <span>{{ $categoryMaster->name ?? '-' }}</span>
                    </div>
                    <div class="mb-4">
                        <h5 class="font-medium text-gray-700">
                            @lang('crud.category_masters.inputs.symbol')
                        </h5>
                        <span>{{ $categoryMaster->symbol ?? '-' }}</span>
                    </div>
                    <div class="mb-4">
                        <h5 class="font-medium text-gray-700">
                            Self Life (in  months)
                        </h5>
                        <span>{{ $categoryMaster->self_life ?? '-' }}</span>
                    </div>
                    <div class="mb-4">
                        <h5 class="font-medium text-gray-700">
                            @lang('crud.category_masters.inputs.description')
                        </h5>
                        <span>{{ $categoryMaster->description ?? '-' }}</span>
                    </div>
                </div>

<div class="mt-10">
<h4 class="mt-4 font-semibold">Customers who bought products from this category:</h4>
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer Name</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Purchase Date</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Average Days Between Purchases</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Predicted Next Purchase Date</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @foreach($userPurchaseData as $data)
                @php
                    $isOverdue = $data['next_purchase_date'] && $data['next_purchase_date']->isPast();
                @endphp
                <tr class="{{ $isOverdue ? 'bg-red-100' : '' }}">
                    <td class="px-6 py-4 whitespace-nowrap">{{ $data['name'] }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $data['last_purchase_date'] ? $data['last_purchase_date']->toDateString() : 'N/A' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $data['average_days'] }} days</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $data['next_purchase_date'] ? $data['next_purchase_date']->toDateString() : 'N/A' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

                <div class="mt-10">
                    <a
                        href="{{ route('category-masters.index') }}"
                        class="button"
                    >
                        <i class="mr-1 icon ion-md-return-left"></i>
                        @lang('crud.common.back')
                    </a>

                    @can('create', App\Models\CategoryMaster::class)
                    <a
                        href="{{ route('category-masters.create') }}"
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
