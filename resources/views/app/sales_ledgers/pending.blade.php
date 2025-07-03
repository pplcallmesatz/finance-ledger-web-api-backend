<div>
<x-app-layout>
    <x-slot name="header">
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="border-slate-300">
                            <h3 class="text-lg	font-semibold	">Total Pendings</h3>
                            <div class="flex flex-wrap mt-4 mb-4">
                                <div class="py-3 px-8 w-full md:w-1/4 lg:w-1/4 text-center bg-green-100">
                                    <h4 class="text-2xl	text-green-700 font-semibold">{{formatCurrency($pendingSum)}}</h4>
                                    <div class="text-green-700">Total Pendings</div>
                                </div>
                              
                                
                            </div>  
                        
                        </div>
            <x-partials.card>
                <x-slot name="title">
                
                </x-slot>

                <div class="overflow-x-auto	">
                @if($getPurchaseData->isEmpty())
                        <p>Hurry :) - No Pendings</p>
                    @else
                        <table class="table-auto w-full">
                            <thead class="bg-slate-300">
                                <tr>
                                    <!-- <th class="px-4 py-2">ID</th> -->
                                    <th class="">Reminder</th>
                                    <th class="px-4 py-2">Date</th>
                                    <th class="px-4 py-2 whitespace-nowrap">Name</th>
                                <th>Payment Status</th>
                                    <th class="px-4 py-2 text-left whitespace-nowrap">Payment Method</th>
                                    <th class="px-4 py-2 text-rightwhitespace-nowrap">Total Price</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($getPurchaseData as $purchase)
                                    <tr class="border border-slate-200	 px-4 py-2" data-tableParent="{{$purchase->id}}">
                                        <!-- <td class="border px-4 py-2">{{ $purchase->id }}</td> -->
                                        <td>
                                        @if($purchase->user->phone)

@php
    $paymentLink = '';
    $phoneNumber = $purchase->user->phone;
    $userName =  $purchase->user->name;
    $salesDate =  $purchase->sales_date;
    $customerPrice = formatCurrency($purchase->total_customer_price);
    $productsList = $purchase->products->pluck('pivot.product_name')->implode(', ')
@endphp

@if($purchase->payment_link)
    @php
        $paymentLink = $purchase->payment_link;
    @endphp
@else
    @php
        $paymentLink = '{Your Payment Link}';
    @endphp
@endif
                                        
    <a class="rounded-md bg-green-50 mx-2 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20" target="_blank" href="{Your Whatsapp Link}">
        Eng</a>

<a class="rounded-md bg-green-50 mx-2 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20" href="{Your Whatsapp Link}" target="_blank">Tam</a>

    @else   
                                            No Number
                                            @endif
                                        </td>
                                        <td class="border border-slate-200	 px-4 py-2">
                                        <a href="{{ route('sales-ledgers.show', $purchase->id) }}" >   {{ $purchase->sales_date }}</a></td>
                                         <td  class="border border-slate-200	 px-4 py-2">
                                         <a href="{{ route('users.show', $purchase->user_id ) }}" >{{ $purchase->user->name  }} 
                                         </a></td>
                                         </td>
                                        <td class="payment_link_row  px-4 py-2">
                                        
                                        @if($purchase->payment_link)
                                            @if($purchase->payment_link_status == "paid")
                                            {!! getRazorpayPaymentStatusHtml($purchase->payment_link_status) !!}
                                            @else 
                                            <a class="payment_link cursor-pointer text-sky-700" href="{{$purchase->payment_link}}" target="_blank">{{$purchase->payment_link}}</a> 
                                            @endif
                                        @else   
                                            <a class="generatePaymentLink cursor-pointer rounded-md bg-sky-50 mx-2 px-2 py-1 text-xs font-medium text-sky-700 ring-1 ring-inset ring-sky-600/20	" data-id="{{$purchase->id}}">Generate Link</a>
                                        @endif
                                        </td>
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
                </div>
            </x-partials.card>
</div>
</div>
@push('scripts')
    <script>
        $(document).ready(function() {
            // $('.generatePaymentLink').click(function() {
            
            //     var salesLedgerId = $(this).attr('data-id');
            //     console.log(salesLedgerId)
            //     $.ajax({
            //         url: '/create-payment-link/' + salesLedgerId, // Adjust route as needed
            //         type: 'GET',
            //         data: {
            //             // Additional data if required
            //         },
            //         success: function(response) {
            //             console.log(response.payment_url);
            //             // You can display the payment URL or redirect to a payment page
            //         },
            //         error: function(error) {
            //             console.error(error);
            //         }
            //     });
            // });
        


        $('.generatePaymentLink').on('click', function() {
            var amount = $('#amount').val();
            var description = $('#description').val();
            var salesLedgerId = $(this).attr('data-id');

            $.ajax({
                url: '{{ route('payment.create') }}',
                type: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    amount: amount,
                    description: description,
                    sales_ledger_id: salesLedgerId
                },
                success: function(response) {
                    console.log(response.sales_ledger_id);
                    $(document).find('[data-tableParent="'+response.sales_ledger_id+'"] .payment_link_row').html('');
                    $(document).find('[data-tableParent="'+response.sales_ledger_id+'"] .payment_link_row').append('<a class="payment_link cursor-pointer text-sky-700" href="'+response.payment_link+'" target="_blank">'+response.payment_link+' - <span class="rounded-md bg-sky-50 mx-2 px-2 py-1 text-xs font-medium text-sky-700 ring-1 ring-inset ring-sky-600/20">Issued</span></a>');
                    console.log('Payment link created:', response.payment_link);
                },
                error: function(error) {
                    console.error('Error creating payment link:', error);
                }
            });
        });
    });
    </script>
@endpush
</x-app-layout>
</div>

