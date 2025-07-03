@php $editing = isset($transaction) @endphp

<div class="flex flex-wrap">
    <x-inputs.group class="w-full">
        <x-inputs.number
            name="bank_balance"
            label="Bank Balance"
            :value="old('bank_balance', ($editing ? $transaction->bank_balance : ''))"
            step="0.01"
            placeholder="Bank Balance"
            required
        ></x-inputs.number>
    </x-inputs.group>

    <x-inputs.group class="w-full">
        <x-inputs.number
            name="cash_in_hand"
            label="Cash In Hand"
            :value="old('cash_in_hand', ($editing ? $transaction->cash_in_hand : ''))"
            step="0.01"
            placeholder="Cash In Hand"
            required
        ></x-inputs.number>
    </x-inputs.group>

    <x-inputs.group class="w-full">
        <x-inputs.textarea name="reason" label="Reason" maxlength="255"
            >{{ old('reason', ($editing ? $transaction->reason : ''))
            }}</x-inputs.textarea
        >
    </x-inputs.group>
</div>
