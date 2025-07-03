@php $editing = isset($expenseLedger) @endphp

<div class="flex flex-wrap">
    <x-inputs.group class="w-1/2 py-2">
        <x-inputs.text
            name="name"
            label="Name"
            :value="old('name', ($editing ? $expenseLedger->name : ''))"
            placeholder="Name"
            required
        ></x-inputs.text>
    </x-inputs.group>
    <x-inputs.group class="w-1/2 py-2">
        <x-inputs.text
            name="invoice_number"
            label="Invoice Number"
            :value="old('invoice_number', ($editing ? $expenseLedger->invoice_number : ''))"
            placeholder="Invoice Number"
            required
        ></x-inputs.text>
    </x-inputs.group>

    <x-inputs.group class="w-1/2 py-2">
        <x-inputs.number
            name="purchase_price"
            label="Purchase Price"
            :value="old('purchase_price', ($editing ? $expenseLedger->purchase_price : ''))"
            step="0.01"
            placeholder="Purchase Price"
            required
        ></x-inputs.number>
    </x-inputs.group>

    <x-inputs.group class="w-1/2 py-2">
        <x-inputs.date
            name="purchase_date"
            label="Purchase Date"
            value="{{ old('purchase_date', ($editing ? optional($expenseLedger->purchase_date)->format('Y-m-d') : '')) }}"
            required
        ></x-inputs.date>
    </x-inputs.group>
        <div class="px-4 my-2 w-1/2 py-2">
        <label class="label font-medium text-gray-700" for="payment_method">
        Payment Method
    </label>
    <select type="text" id="payment_method" name="payment_method" value="" class="block appearance-none w-full py-1 px-2 text-base leading-normal text-gray-800 border border-gray-200 rounded" required="required" autocomplete="off">
    <option value="cash">Cash</option>
    <option value="bank">Bank Transfer</option>
    </select>
    </div>

    <div class="px-4 my-2 w-1/2 py-2">
        <label class="label font-medium text-gray-700" for="payment_method">
        Expense Type
    </label>
    <select type="text" id="expense_type" name="expense_type" value="" class="block appearance-none w-full py-1 px-2 text-base leading-normal text-gray-800 border border-gray-200 rounded" required="required" autocomplete="off">
        <option value="Raw Material">Raw Material</option>
        <option value="Machinary">Machinary</option>
        <option value="Packing">Packing</option>
        <option value="Marketing">Marketing</option>
        <option value="Travel Expense">Travel Expense</option>
        <option value="Electricity">Electricity</option>
        <option value="Labour">Labour</option>
        <option value="Legal">Legal</option>
        <option value="Research">Research</option>
    </select>
    </div>

    <x-inputs.group class="w-1/2 py-2">
        <x-inputs.textarea name="seller" label="Seller"  required
            >{{ old('seller', ($editing ? $expenseLedger->seller : ''))
            }}</x-inputs.textarea
        >
    </x-inputs.group>

    <x-inputs.group class="w-1/2 py-2">
        <x-inputs.textarea
            name="description"
            label="Description"
            required
            >{{ old('description', ($editing ? $expenseLedger->description :
            '')) }}</x-inputs.textarea
        >
    </x-inputs.group>

    <x-inputs.group class="w-1/2 py-2">
        <x-inputs.checkbox name="deduct" value="deduct" label="Deduct From Account" />
    </x-inputs.group>

</div>
