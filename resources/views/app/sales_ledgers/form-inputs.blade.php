@php $editing = isset($salesLedger) @endphp

<div class="flex flex-wrap">
    @if(!$editing )
        <div class="px-4 my-2 w-full md:w-1/2">
            <input type="checkbox" value="new" name="userCheck" id="userCheck">
            <label for="userCheck">New User</label>
        </div>
    @endif
    
<div class="w-full"></div>
<div class="chooseUser w-full">
<x-inputs.group class="w-full md:w-1/2">
    <x-inputs.select name="user_id" label="User" required class="userlist">
        @php $selected = old('user_id', ($editing ? $salesLedger->user_id : '')) @endphp
        <option disabled {{ empty($selected) ? 'selected' : '' }}>Please select the User</option>
        
        @foreach($users as $user)
            <option value="{{ $user->id }}" {{ $selected == $user->id ? 'selected' : '' }}>
                {{ $user->name }} - ({{ $user->phone }})
            </option>
        @endforeach
    </x-inputs.select>
</x-inputs.group>
</div>
@if(!$editing)
<div id="createCustomer" class="hidden flex flex-wrap w-full">
        <x-inputs.group class="w-full md:w-1/2">
            <x-inputs.text
                name="name"
                label="Name"
                maxlength="255"
                placeholder="Customer name"
            ></x-inputs.text>
        </x-inputs.group>
        <x-inputs.group class="w-full md:w-1/2">
            <x-inputs.text
                name="email"
                label="Email"
                maxlength="255"
                placeholder="Customer email"
            ></x-inputs.text>
        </x-inputs.group>

        <x-inputs.group class="w-full md:w-1/2">
            <x-inputs.text
                name="phone"
                label="Phone"
                maxlength="255"
                placeholder="Customer phone"
            ></x-inputs.text>
        </x-inputs.group>

        <x-inputs.group class="w-full md:w-1/2">
            <x-inputs.textarea
                name="remarks"
                label="Remarks"
                maxlength="255"
                placeholder="Customer remarks"
            ></x-inputs.textarea>
        </x-inputs.group>
</div>
@endif
<div class="w-full"></div>
<!-- <x-inputs.group class="w-1/2">
        <x-inputs.select name="user_id" label="User" required>
            @php $selected = old('user_id', ($editing ? $salesLedger->user_id : '')) @endphp
            <option disabled {{ empty($selected) ? 'selected' : '' }}>Please select the User</option>
            
            @foreach($users as $value => $label)
            
            <option value="{{ $value }}" {{ $selected == $value ? 'selected' : '' }} >{{ $label }}</option>
            @endforeach
        </x-inputs.select>
    </x-inputs.group> -->
    <x-inputs.group class="w-full md:w-full">
        <x-inputs.date
            name="sales_date"
            label="Sales Date"
            :value="old('sales_date', ($editing ? $salesLedger->sales_date : ''))"
        ></x-inputs.date>
    </x-inputs.group>
    <x-inputs.group class="w-full md:w-1/2 hidden">
        <x-inputs.text
            name="invoice_number"
            label="Invoice Number"
            :value="old('invoice_number', ($editing ? $salesLedger->invoice_number : ''))"
            maxlength="255"
            placeholder="Invoice Number"
        ></x-inputs.text>
    </x-inputs.group>
    
    <x-inputs.group class="w-full md:w-1/2">
        <x-inputs.textarea name="remarks" label="Remarks" maxlength="255"
            >{{ old('remarks', ($editing ? $salesLedger->remarks : ''))
            }}</x-inputs.textarea
        >
    </x-inputs.group>

    <x-inputs.group class="w-full md:w-1/2">
        <x-inputs.textarea
            name="company_address"
            label="Company Address"
            maxlength="255"
            >{{ old('company_address', ($editing ? $salesLedger->company_address
            : '')) }}</x-inputs.textarea
        >
    </x-inputs.group>
    <x-inputs.group class="w-full md:w-1/2">
        <x-inputs.select name="payment_status" label="Payment Status" 
        :value="old('payment_status', ($editing ? $salesLedger->payment_status : ''))"
        required>
            <option>Please select</option>
            <option value="paid" {{old('payment_status',($editing ? $salesLedger->payment_status : '')) == 'paid'?'selected': ''}}>Paid</option>
            <option value="pending" {{old('payment_status',($editing ? $salesLedger->payment_status : '')) == 'pending'?'selected': ''}}>Pending</option>
        </x-inputs.select>
    </x-inputs.group>

    <x-inputs.group class="w-full md:w-1/2">
        <x-inputs.select name="payment_method" label="Payment Method" 
        :value="old('payment_method', ($editing ? $salesLedger->payment_method : ''))"
        required>
            <option >Please select </option>
            <option value="cash"  {{old('payment_method', ($editing ? $salesLedger->payment_method : '')) == 'cash' ? 'selected' : ''}} >Cash</option>
        <option value="bank"  {{old('payment_method', ($editing ? $salesLedger->payment_method : '')) == 'bank' ? 'selected' : ''}} >Bank</option>
        <option value="website"  {{old('payment_method', ($editing ? $salesLedger->payment_method : '')) == 'website' ? 'selected' : ''}} >Website</option>
        </x-inputs.select>
    </x-inputs.group>
</div>
    <div class="flex flex-wrap w-full">
        <x-inputs.group class="w-full md:w-1/2">
            <x-inputs.select name="select_product" label="Choose Product" required id="selectProduct" class="productsList">
                <option>Please select product</option>
                @foreach ($products as $product)
                <option value="{{$product->id}}" data-pp="{{$product->product_price}}" data-sp="{{$product->selling_price}}">{{$product->name}}</option>
                @endforeach
            </x-inputs.select>
        </x-inputs.group>
    </div>
    <div class="px-4 my-4 w-full" style="overflow: auto;">
        <!-- <h4 class="font-bold text-lg text-gray-700">
            Assign @lang('crud.products.name')
        </h4> -->
        <div class="py-2" style="min-width: 1024px;"    >
            <div id="ledgerProductList">
                        <div>
                            <h4 class="font-bold text-lg text-gray-700">Products</h4>
                        </div>
                        <div class="p-1  bg-slate-400" id="ledgerProductListHeader">
                            <div class="flex w-full py-2">
                                <div class="hl-action w-24 text-center" style="width: 10%;">Action</div>
                                <div class="hl-productName  grow" style="width: 15%;">Product Name</div>
                                <div class="hl-productMaster  grow" style="width: 15%;">Batch Number</div>
                                <div class="hl-purchasePrice grow" style="width: 15%;">Purchase Price</div>
                                <div class="hl-sellingPrice grow" style="width: 15%;">Selling Price</div>
                                <div class="hl-customerPrice grow" style="width: 15%;">Customer Price</div>
                                <div class="hl-quantity grow" style="width: 15%;">Quantity</div>
                                <div class="hl-total grow" style="width: 15%;">Total</div>
                            </div>
                        </div>
                        <div class="py-2" id="ledgerProductListBody">
                            @foreach ($products as $product)
                                @php
                                    $productSalesLedger = $editing ? $salesLedger->products()->where('product_id', $product->id)->first() : null;
                                @endphp
                                    @if ($editing && isset($productSalesLedger))
                                        <div class="flex w-full py-2" data-group="group{{ $product->id }}" data-key="{{ $product->id }}">
                                                <div class="bl-action w-24 text-center" style="width: 10%;">
                                                    <input type="hidden" id="product{{ $product->id }}" name="products[{{ $product->id }}][selected]" value="{{ $product->id }}" class="" data-parent="group{{ $product->id }}" checked>
                                                    <input type="hidden" id="products[{{ $product->id }}][product_id]" name="products[{{ $product->id }}][product_id]" value="{{ $product->id }}" class="block appearance-none w-full py-1 px-2 text-base leading-normal text-gray-800 border border-gray-200 rounded" autocomplete="off">
                                                        <div class="bl-delete" data-parent="group{{ $product->id }}">
                                                            <i class="fa fa-trash"></i>
                                                        </div>
                                                </div>
                                                <div class="bl-productName  grow" style="width: 15%;">
                                                    <input type="hidden" id="products[{{ $product->id }}][product_name]" name="products[{{ $product->id }}][product_name]" value="{{$productSalesLedger->pivot->product_name}}" class="block appearance-none w-full py-1 px-2 text-base leading-normal text-gray-800 border border-gray-200 rounded product-name" data-parent="group{{ $product->id }}" readonly="readonly" autocomplete="off">
                                                    {{$productSalesLedger->pivot->product_name}}
                                                </div>

                                                <div class="bl-batchcode" style="width: 15%;">
                                                    <select id="productMaster{{ $product->id }}" id="products[{{ $product->id }}][product_master_id]" name="products[{{ $product->id }}][product_master_id]" value="" class="block appearance-none w-full py-1 px-2 text-base leading-normal text-gray-800 border border-gray-200 rounded product-name" data-parent="group{{ $product->id }}" autocomplete="off" data-selected="{{$productSalesLedger->pivot->product_master_id}}">
                                                    </select>
                                                </div>

                                                <div class="bl-purchasePrice grow" style="width: 15%;">
                                                    <input type="hidden" id="products[{{ $product->id }}][product_price]" name="products[{{ $product->id }}][product_price]" value="{{$productSalesLedger->pivot->product_price}}" class="block appearance-none w-full py-1 px-2 text-base leading-normal text-gray-800 border border-gray-200 rounded product-price" data-parent="group{{ $product->id }}" readonly="readonly" autocomplete="off">
                                                    {{$productSalesLedger->pivot->product_price}}
                                                </div>

                                                <div class="bl-sellingPrice grow" style="width: 15%;">
                                                    <input type="hidden" id="products[{{ $product->id }}][selling_price]" name="products[{{ $product->id }}][selling_price]" value="{{$productSalesLedger->pivot->selling_price}}" class="block appearance-none w-full py-1 px-2 text-base leading-normal text-gray-800 border border-gray-200 rounded selling-price" data-parent="group{{ $product->id }}" step="0.01" autocomplete="off">
                                                    {{$productSalesLedger->pivot->selling_price}}
                                                </div>


                                                <div class="bl-customerPrice grow" style="width: 15%;">
                                                    <input type="number" id="products[{{ $product->id }}][customer_price]" name="products[{{ $product->id }}][customer_price]" value="{{$productSalesLedger->pivot->customer_price}}" class="block appearance-none w-full py-1 px-2 text-base leading-normal text-gray-800 border border-gray-200 rounded customer-price" data-parent="group{{ $product->id }}" step="0.01" autocomplete="off">
                                                </div>


                                                <div class="bl-quantity grow" style="width: 15%;">
                                                    <input type="number" id="products[{{ $product->id }}][quantity]" name="products[{{ $product->id }}][quantity]" value="{{$productSalesLedger->pivot->quantity}}" class="block appearance-none w-full py-1 px-2 text-base leading-normal text-gray-800 border border-gray-200 rounded quantity" data-parent="group{{ $product->id }}" step="1" autocomplete="off">
                                                </div>
                                                <div class="bl-total grow text-right" style="width: 15%;"><span class="total-value">{{$productSalesLedger->pivot->quantity * $productSalesLedger->pivot->customer_price}} </span></div>
                                            </div>
                                    @endif
                            @endforeach   
                        </div>
                        <div id="ledgerProductListFooter" class="flex w-full">
                            <div class="grow" style="width: 10%"></div>
                            <div class="grow" style="width: 15%"></div>
                            <div class="grow" style="width: 15%"></div>
                            <div class="grow" style="width: 15%" id="ppTotal">
                            {{old('payment_method', ($editing ? $salesLedger->total_product_price : ''))}}
                            </div>
                            <div class="grow" style="width: 15%" id="spTotal">
                            {{old('payment_method', ($editing ? $salesLedger->selling_product_price : ''))}}</div>
                            <div class="grow" style="width: 15%"></div>
                            <div class="grow" style="width: 15%"></div>
                            <div class="grow text-right" style="width: 15%" id="cpTotal">
                            {{old('payment_method', ($editing ? $salesLedger->total_customer_price : ''))}}</div>
                        </div>
             </div>
        </div>
    </div>
@push('scripts')
<script>
$(document).ready(function(){
    // $('#user_id').drop2();
    $('.userlist').select2();
    $('.productsList').select2();
    $('.listIterate').each(function(){
        $(this).css({
            "margin-bottom": "50px",
            "background" : "yellow"
        })
        let totalSelling = $(this).find('input.quantity').val() * $(this).find('input.customer-price').val();
        let htl= '<div class="p-4">Total: <span class="total-value">'+totalSelling+'</span></div><br><hr><br></div>';
        $(this).append(htl);
    });

    $(document).on('keyup', '.quantity', function(){
            let dp = $(this).attr('data-parent');
            customerPriceCalculation(dp);
    });
    $(document).on('keyup', '.customer-price', function(){
            let dp = $(this).attr('data-parent');
            customerPriceCalculation(dp);
    });

    function customerPriceCalculation(dparent){
        let dgroup =  $('[data-group="'+dparent+'"]');
        let tv = dgroup.find('.customer-price').val() * dgroup.find('.quantity').val();
        dgroup.find(".total-value").text(tv);
        overAllTotal();
    }
    $(document).on('click', ".bl-delete", function(){
        let tar = $(this).attr('data-parent');
        $('[data-group="'+tar+'"]').remove();
        overAllTotal();
    });
    function overAllTotal(){
        // customer-price
        let pp=0;
        let sp=0;
        let cp=0;
        $('[data-group]').each(function(){
            let c = $(this).attr('data-key');
            let ccp = ($(this).find('.customer-price').val() * $(this).find('.quantity').val()).toFixed(2);
          
            // console.log($(this).find('input.product-name').val());
            // console.log($(this).find('input.product-price').val());
            // console.log($(this).find('input.selling-price').val());
             cp = parseFloat(cp) + parseFloat(ccp);
             sp =parseFloat(sp) + parseFloat($(this).find('.selling-price').val() * $(this).find('.quantity').val());
             pp = parseFloat(pp) + parseFloat($(this).find('.product-price').val() * $(this).find('.quantity').val());
            $(this).find(".total-value").text(ccp);
        });
        $('#ppTotal').text(pp);
        $('#spTotal').text(sp);
        $('#cpTotal').text(cp);
    }
    $('#select_product').on('change', function(){
        // console.log($(this).find(":selected").val())
        let productId = $(this).find(":selected").val();
        let productName = $(this).find(":selected").text();
        let productPurchasePrice = $(this).find(":selected").attr('data-pp');
        let productSellingPrice = $(this).find(":selected").attr('data-sp');
        
        let listContent = `<div class="flex w-full py-2" data-group="group${productId}" data-key="${productId}">
                            <div class="bl-action w-24 text-center" style="width: 10%;">
                            <input type="hidden" id="product${productId}" name="products[${productId}][selected]" value="${productId}" class="" data-parent="group${productId}" checked>
                            <input type="hidden" id="products[${productId}][product_id]" name="products[${productId}][product_id]" value="${productId}" class="block appearance-none w-full py-1 px-2 text-base leading-normal text-gray-800 border border-gray-200 rounded" autocomplete="off">
                                <div class="bl-delete" data-parent="group${productId}">
                                    <i class="fa fa-trash"></i>
                                </div>
                            </div>
                            <div class="bl-productName  grow" style="width: 15%;">                            
                            <input type="hidden" id="products[${productId}][product_name]" name="products[${productId}][product_name]" value="${productName}" class="block appearance-none w-full py-1 px-2 text-base leading-normal text-gray-800 border border-gray-200 rounded product-name" data-parent="group${productId}" readonly="readonly" autocomplete="off">
                            ${productName}
                            </div>
                            <div class="bl-batchcode" style="width: 15%;">
                                <select id="productMaster${productId}" id="products[${productId}][product_master_id]" name="products[${productId}][product_master_id]" value="" class="block appearance-none w-full py-1 px-2 text-base leading-normal text-gray-800 border border-gray-200 rounded product-name" data-parent="group${productId}" autocomplete="off">
                                </select>
                            </div>
                            <div class="bl-purchasePrice grow" style="width: 15%;">
                            <input type="hidden" id="products[${productId}][product_price]" name="products[${productId}][product_price]" value="${productPurchasePrice}" class="block appearance-none w-full py-1 px-2 text-base leading-normal text-gray-800 border border-gray-200 rounded product-price" data-parent="group${productId}" readonly="readonly" autocomplete="off">
                            ${productPurchasePrice}
                            </div>

                            <div class="bl-sellingPrice grow" style="width: 15%;">
                            <input type="hidden" id="products[${productId}][selling_price]" name="products[${productId}][selling_price]" value="${productSellingPrice}" class="block appearance-none w-full py-1 px-2 text-base leading-normal text-gray-800 border border-gray-200 rounded selling-price" data-parent="group${productId}" step="0.01" autocomplete="off">
                            ${productSellingPrice}
                            </div>


                            <div class="bl-customerPrice grow" style="width: 15%;">
                            <input type="number" id="products[${productId}][customer_price]" name="products[${productId}][customer_price]" value="${productSellingPrice}" class="block appearance-none w-full py-1 px-2 text-base leading-normal text-gray-800 border border-gray-200 rounded customer-price" data-parent="group${productId}" step="0.01" autocomplete="off">
                            </div>


                            <div class="bl-quantity grow" style="width: 15%;">
                            <input type="number" id="products[${productId}][quantity]" name="products[${productId}][quantity]" value="1" class="block appearance-none w-full py-1 px-2 text-base leading-normal text-gray-800 border border-gray-200 rounded quantity" data-parent="group${productId}" step="1" autocomplete="off">
                            </div>
                            <div class="bl-total grow text-right" style="width: 15%;"><span class="total-value"></span></div>
                        </div>`;
                        if ($("#ledgerProductListBody").find(`[data-key="${productId}"]`).length > 0 ){
                            alert('Product already added in the list: '+ productId);    
                        }
                        else{
                            $("#ledgerProductListBody").append(listContent);
                            overAllTotal();
                        }

                        toGetProdutFromMaster(productId,'add');

    });


    
    function toGetProdutFromMaster(data, module){
        var productId = data;
            if (productId) {
                let url = "{{ route('products.get', ['product_id' => 'product_id', 'module' => 'module']) }}";
                $.ajax({
                    url: url.replace('product_id', productId),
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        $('#productMaster'+productId).empty();
                        $('#productMaster'+productId).append('<option disabled selected>Choose product</option>');
                        // $('#product_id').append('<option disabled selected>Choose product</option>');
                        if(data.length > 0){
                            let tc = data.length;
                            $.each(data, function(key, product) {
                                if((tc === 1)){
                                    $('#productMaster'+productId).append('<option value="'+ product.id +'" selected>'+ product.batch_number +'</option>');    
                                }
                                else{
                                    $('#productMaster'+productId).append('<option value="'+ product.id +'">'+ product.batch_number +'</option>');
                                }
                                
                            });
                        }
                        else{
                            $('#productMaster'+productId).append('<option value="0">No Batch Code</option>');
                        }
                        let ds = $("#productMaster"+productId).attr("data-selected");
                        if(ds != null){
                            $("#productMaster"+productId).find('option[value="'+ds+'"]').attr('selected', 'selected')
                        }
                    }
                });
            } else {
                $('#product_id').empty();
                $('#product_id').append('<option disabled selected>Choose product</option>');
            }
    }


    @if($editing)
            
            $("#ledgerProductListBody > div").each(function(){
                let key = $(this).attr('data-key');
                toGetProdutFromMaster(key, 'update');
            })
    @endif

    $(document).on('change','#userCheck',function(){
        
        if($(this).is(':checked')){
                  $("#user_id").hide();
                  $('#createCustomer').removeClass('hidden');
        }
    });
});                
let htm = `<div class="listIterate" data-group="group1">
                <div>
                    <div class="relative block mb-2">
                        <input type="checkbox" id="product1" name="products[1][selected]" value="1" class="" data-parent="group1">
                        <label class="text-gray-700 pl-2" for="product1">Sdkhgh</label>
                    </div>
                </div>
                <div>
                    <input type="hidden" id="products[1][product_id]" name="products[1][product_id]" value="1" class="block appearance-none w-full py-1 px-2 text-base leading-normal text-gray-800 border border-gray-200 rounded" autocomplete="off">
                    
                    <label class="label font-medium text-gray-700" for="products[1][product_name]">Product Name</label>
                    <input type="text" id="products[1][product_name]" name="products[1][product_name]" value="sdkhgh" class="block appearance-none w-full py-1 px-2 text-base leading-normal text-gray-800 border border-gray-200 rounded product-name" data-parent="group1" readonly="readonly" autocomplete="off">
                    
                    <label class="label font-medium text-gray-700" for="products[1][product_price]">Product Price</label>
                    <input type="number" id="products[1][product_price]" name="products[1][product_price]" value="369.06" class="block appearance-none w-full py-1 px-2 text-base leading-normal text-gray-800 border border-gray-200 rounded product-price" data-parent="group1" readonly="readonly" autocomplete="off">

                    <label class="label font-medium text-gray-700" for="products[1][selling_price]">Selling Price</label>
                    <input type="number" id="products[1][selling_price]" name="products[1][selling_price]" value="123.05" class="block appearance-none w-full py-1 px-2 text-base leading-normal text-gray-800 border border-gray-200 rounded selling-price" data-parent="group1" step="0.01" autocomplete="off">

                    <label class="label font-medium text-gray-700" for="products[1][customer_price]">Customer Price</label>
                    <input type="number" id="products[1][customer_price]" name="products[1][customer_price]" value="123.05" class="block appearance-none w-full py-1 px-2 text-base leading-normal text-gray-800 border border-gray-200 rounded customer-price" data-parent="group1" step="0.01" autocomplete="off">


                    <label class="label font-medium text-gray-700" for="products[1][quantity]">Quantity</label>
                    <input type="number" id="products[1][quantity]" name="products[1][quantity]" value="" class="block appearance-none w-full py-1 px-2 text-base leading-normal text-gray-800 border border-gray-200 rounded quantity" data-parent="group1" step="0.01" autocomplete="off">
                </div>
            </div>`;


    </script>



@endpush