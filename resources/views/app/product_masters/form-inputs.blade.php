@php $editing = isset($productMaster) @endphp

<div class="flex flex-wrap">
    <x-inputs.group class="w-full">
        <x-inputs.select name="category_id" label="Category Master" required>
            @php $selected = old('category_id', ($editing ? $productMaster->category_id : '')) @endphp
            <option disabled {{ empty($selected) ? 'selected' : '' }}>Please select the Category Master</option>
            @foreach($categoryMasters as $value )
            <option self-life="{{ $value->self_life }}" value="{{ $value->id }}" {{ $selected == $value->id ? 'selected' : '' }} >{{ $value->name }}</option>
            @endforeach
        </x-inputs.select>
    </x-inputs.group>

    <x-inputs.group class="w-full lg:w-6/12">
        <x-inputs.text
            name="name"
            label="Name"
            :value="old('name', ($editing ? $productMaster->name : ''))"
            placeholder="Name"
            required
        ></x-inputs.text>
    </x-inputs.group>


   

    
    <div class="flex flex-warp w-full mt-8">
    <x-inputs.group class="w-full lg:w-6/12">
        <x-inputs.date
            name="purchase_date"
            label="Purchase Date"
            value="{{ old('purchase_date', ($editing ? optional($productMaster->purchase_date)->format('Y-m-d') : '')) }}"
            required
        ></x-inputs.date>
    </x-inputs.group>
    <x-inputs.group class="w-full lg:w-6/12">
        <x-inputs.date
            name="manufacturing_date"
            label="Manufacturing Date"
            value="{{ old('manufacturing_date', ($editing ? optional($productMaster->manufacturing_date)->format('Y-m-d') : '')) }}"
            required
        ></x-inputs.date>
    </x-inputs.group>
    <x-inputs.group class="w-full lg:w-6/12">
        <x-inputs.date
            name="expire_date"
            label="Expire Date"
            value="{{ old('expire_date', ($editing ? optional($productMaster->expire_date)->format('Y-m-d') : '')) }}"
            required
        ></x-inputs.date>
    </x-inputs.group>
    </div>

    <div class="flex flex-warp w-full mt-8 mb-8">

    <x-inputs.group class="md:w-1/4">
        <x-inputs.number
            name="purchase_price"
            label="Purchase Price"
            :value="old('purchase_price', ($editing ? $productMaster->purchase_price : ''))"
            step="0.01"
            placeholder="Purchase Price"
            required
        ></x-inputs.number>
    </x-inputs.group>
    <x-inputs.group class="md:w-1/4">
        <x-inputs.number
            name="transportation_cost"
            label="Transportation Cost"
            :value="old('transportation_cost', ($editing ? $productMaster->transportation_cost : ''))"
            step="0.01"
            placeholder="Transportation Cost"
            required
        ></x-inputs.number>
    </x-inputs.group>
    <x-inputs.group class="md:w-1/4">
        <x-inputs.number
            name="quantity_purchased"
            label="Quantity Purchased"
            :value="old('quantity_purchased', ($editing ? $productMaster->quantity_purchased : ''))"
            placeholder="Quantity Purchased"
            required
        ></x-inputs.number>
    </x-inputs.group>
    <div class="px-4 my-2 md:w-1/4">
                        <span class="text-sm">Per Unit cost</span>    
                        <h5 class="font-medium text-gray-700 text-lg">
                        @if($editing)
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
                                   <span class="rounded-md bg-green-50 px-2 py-1 font-medium text-green-700 ring-1 ring-inset ring-green-600/20" id="perUnitCost"> {{ formatCurrency($result) }}</span> / <span class="text-xs" id="totalCost">{{formatCurrency($totalCost)}} (Total Cost)</span>
                            @else
                            <span class="rounded-md bg-green-50 px-2 py-1 font-medium text-green-700 ring-1 ring-inset ring-green-600/20" id="perUnitCost"> </span> / <span class="text-xs" id="totalCost"></span>    
                            @endif

                        </h5>
    </div>
</div>
    <x-inputs.group class="w-full lg:w-6/12">
        <x-inputs.text
            name="invoice_number"
            label="Invoice Number"
            :value="old('invoice_number', ($editing ? $productMaster->invoice_number : ''))"
            placeholder="Invoice Number"
            required
            
        ></x-inputs.text>
    </x-inputs.group>

    
    <x-inputs.group class="w-full lg:w-6/12 ">
        <x-inputs.number
            name="total_piece"
            label="Available Quantity"
            :value="old('total_piece', ($editing ? $productMaster->total_piece : ''))"
            placeholder="Available Quantity"
            required
            step="0.01"
            min="0.25"
        ></x-inputs.number>
    </x-inputs.group>
    
    <x-inputs.group class="w-full">
        <x-inputs.textarea name="vendor" label="Vendor" maxlength="255"
            >{{ old('vendor', ($editing ? $productMaster->vendor : ''))
            }}</x-inputs.textarea
        >
    </x-inputs.group>
</div>

@push('scripts')
<script>
    $(document).ready(function(){
        $("#purchase_price").on('change', function(){
            perUnitCost()
        });
        $("#transportation_cost").on('change', function(){
            perUnitCost()
        });
        $("#quantity_purchased").on('change', function(){
            perUnitCost()
        });
        function perUnitCost(){
            let pp = parseFloat($("#purchase_price").val());
            let tc = parseFloat($("#transportation_cost").val());
            let qp = parseFloat($("#quantity_purchased").val());
            let tpc = pp + tc;
            let puc = tpc / qp;
            $("#perUnitCost").text(puc.toFixed(2));
            $("#totalCost").text(tpc.toFixed(2)+" (Total Cost)");
        }
        // Function to add months to a date
        function addMonthsToDate(date, months) {
                var newDate = new Date(date);
                newDate.setMonth(newDate.getMonth() + parseInt(months));
                // If the day of the month is different after adding months, adjust the date
                // This handles cases where the target month has fewer days (e.g., adding to January 31)
                if (newDate.getDate() != date.getDate()) {
                    newDate.setDate(0);
                }
                newDate.setDate(newDate.getDate() - 1);

                return newDate;
            }

            // Function to format the date
            function formatDate(date) {
                const options = { year: 'numeric', month: 'long', day: 'numeric' };
                return date.toLocaleDateString(undefined, options);
            }
            function formatDateToInputValue(date) {
                var year = date.getFullYear();
                var month = ('0' + (date.getMonth() + 1)).slice(-2); // Months are zero based
                var day = ('0' + date.getDate()).slice(-2);
                return `${year}-${month}-${day}`;
            }
        $(document).on('blur','#manufacturing_date', function(){
            let selfLife = $('#category_id').find('option:selected').attr('self-life');            
            
            let mDate = $(this).val();                
            var originalDate = new Date(mDate);
            if(originalDate != "Invalid Date"){
            if(selfLife!=undefined){
                let mDate = $(this).val();                
                // Add 26 months to the original date
                var newDate = addMonthsToDate(originalDate, selfLife);
                // Format the new date
                $("#expire_date").val(formatDateToInputValue(newDate));
                var formattedNewDate = formatDate(newDate);
            }
            else if(selfLife === undefined){
                alert("Choose atleast 1 category");
            }
        }
        })
    })
    </script>
    @endpush