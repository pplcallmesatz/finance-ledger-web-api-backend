@php $editing = isset($product) @endphp

<div class="flex flex-wrap">
    <x-inputs.group class="w-full">
        <x-inputs.select
            name="category_master_id"
            label="Category Master"
            required
        >
            @php $selected = old('category_master_id', ($editing ? $product->category_master_id : '')) @endphp
            <option disabled {{ empty($selected) ? 'selected' : '' }}>Please select the Category Master</option>
            @foreach($categoryMasters as $value => $label)
            <option value="{{ $value }}" {{ $selected == $value ? 'selected' : '' }} >{{ $label }}</option>
            @endforeach
        </x-inputs.select>
    </x-inputs.group>

    <x-inputs.group class="w-full lg:w-6/12 mt-8">
        <x-inputs.text
            name="name"
            label="Name"
            :value="old('name', ($editing ? $product->name : ''))"
            placeholder="Name"
            required
        ></x-inputs.text>
    </x-inputs.group>
    <x-inputs.group class="w-full lg:w-6/12 mt-8">
        <x-inputs.text
            name="units"
            label="Unit"
            :value="old('units', ($editing ? $product->units : ''))"
            placeholder="Units"
            required
        ></x-inputs.text>
    </x-inputs.group>

    <div class="w-full flex flex-wrap">

        <x-inputs.group class="w-full md:w-1/4 mt-8">
            <x-inputs.number
                name="purchase_price"
                label="Purchase Price"
                :value="old('purchase_price', ($editing ? $product->purchase_price : ''))"
                step="0.01"
                placeholder="Purchase Price"
                required
            ></x-inputs.number>
        </x-inputs.group>

        <x-inputs.group class="w-full md:w-1/4 mt-8">
            <x-inputs.number
                name="packing_price"
                label="Packing Price"
                :value="old('packing_price', ($editing ? $product->packing_price : ''))"
                step="0.01"
                placeholder="Packing Price"
                required
            ></x-inputs.number>
        </x-inputs.group>
        <div class="mt-8 mb-4 w-1/4">
            <span class="text-sm">Product Price</span>    
            <h5 class="font-medium text-gray-700 text-lg" id="product_price">
            {{($editing ? ($product->purchase_price + $product->packing_price):'')}}
            </h5>
        </div>
        <x-inputs.group class="w-full md:w-1/4 mt-8">
            <x-inputs.number
                name="selling_price"
                label="Selling Price"
                :value="old('selling_price', ($editing ? $product->selling_price : ''))"
                step="0.01"
                placeholder="Selling Price"
                required
            ></x-inputs.number>
            <span class="text-sm" id="profitValue"></span>
        </x-inputs.group>
    </div>
    

    <div class="w-full flex flex-wrap mt-8">

            <x-inputs.group class="w-1/2">
                <x-inputs.text
                    name="barcode"
                    label="Barcode"
                    :value="old('barcode', ($editing ? $product->barcode : ''))"
                    ></x-inputs.text
                >
            </x-inputs.group>

            <x-inputs.group class="w-1/2">
                <x-inputs.textarea
                    name="barcode_vendor"
                    label="Barcode Vendor"
                    >{{ old('barcode_vendor', ($editing ? $product->barcode_vendor : ''))
                    }}</x-inputs.textarea
                >
            </x-inputs.group>
    </div>
    <x-inputs.group class="w-full lg:w-6/12">
        <x-inputs.textarea
            name="description"
            label="Description"
            >{{ old('description', ($editing ? $product->description : ''))
            }}</x-inputs.textarea
        >
    </x-inputs.group>
</div>


@push('scripts')
<script>
    $(document).ready(function(){

            $(document).on('change', '#purchase_price', function(){
                calculateProductPrice()  
            });
            $(document).on('change', '#packing_price', function(){
                calculateProductPrice()  
            });
        function calculateProductPrice(){
            let pp = parseInt($('#purchase_price').val());
            let pkp = parseInt($('#packing_price').val());
            let tppkp = pp + pkp;
            
            $('#product_price').text(tppkp);
            sellingPrice();
        }
        function sellingPrice(){
            let tppkp =  parseInt($('#product_price').text());
            let sP = tppkp * 2;
            $('#selling_price').val(sP);
            $('#profitValue').html("Profit value is <strong class='rounded-md bg-green-50 px-2 py-1 text-green-700 ring-1 ring-inset ring-green-600/20'>"+(sP-tppkp)+"</strong>")
        }

    })
</script>
@endpush