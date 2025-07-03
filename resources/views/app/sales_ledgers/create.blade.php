<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            @lang('crud.sales_ledgers.create_title')
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <x-partials.card>
                <x-slot name="title">
                    <a href="{{ route('sales-ledgers.index') }}" class="mr-4"
                        ><i class="mr-1 icon ion-md-arrow-back"></i
                    ></a>
                    @lang('crud.sales_ledgers.create_title')
                </x-slot>

                <x-form
                    method="POST"
                    action="{{ route('sales-ledgers.store') }}"
                    class="mt-4"
                >
                    @include('app.sales_ledgers.form-inputs')


                    

                    <div class="mt-10">
                        <a
                            href="{{ route('sales-ledgers.index') }}"
                            class="button"
                        >
                            <i
                                class="
                                    mr-1
                                    icon
                                    ion-md-return-left
                                    text-primary
                                "
                            ></i>
                            @lang('crud.common.back')
                        </a>

                        <button
                            type="submit"
                            class="button button-primary float-right" id="submitButton" disabled
                        >
                            <i class="mr-1 icon ion-md-save"></i>
                            @lang('crud.common.create')
                        </button>
                    </div>
                </x-form>
            </x-partials.card>
        </div>
    </div>
    @push('scripts')
    <script>
        
        function checkDisabled(){
            var salesDate = $('#sales_date').val();
            var productDivs = $('#ledgerProductListBody > *').length;

        // Check if sales_date is not empty and there's at least one product div
        if (salesDate !== '' && productDivs >= 1) {
             // Enable the submit button
             $('#submitButton').prop('disabled', false);
        } else {
            // Disable the submit button
            $('#submitButton').prop('disabled', true);   

        }

        }
        $(document).ready(function(){
            checkDisabled();
            $('#sales_date').on('change', function(){
                checkDisabled();
            })
            $('#select_product').on('change', function(){
                checkDisabled();
            })
        })
    </script>
    @endpush
</x-app-layout>

