@php $editing = isset($categoryMaster) @endphp

<div class="flex flex-wrap">
    <x-inputs.group class="w-full lg:w-6/12">
        <x-inputs.text
            name="name"
            label="Name"
            :value="old('name', ($editing ? $categoryMaster->name : ''))"
            placeholder="Name"
            required
        ></x-inputs.text>
    </x-inputs.group>

    <x-inputs.group class="w-full lg:w-6/12">
        <x-inputs.text
            name="symbol"
            label="Symbol"
            :value="old('symbol', ($editing ? $categoryMaster->symbol : ''))"
            maxlength="255"
            placeholder="Symbol"
        ></x-inputs.text>
    </x-inputs.group>
    <x-inputs.group class="w-full lg:w-6/12">
        <x-inputs.number
            name="self_life"
            label="self_life"
            :value="old('self_life', ($editing ? $categoryMaster->self_life : ''))"
            maxlength="255"
            placeholder="Self Life in months"
        ></x-inputs.number>
    </x-inputs.group>
    
    <x-inputs.group class="w-full">
        <x-inputs.textarea
            name="description"
            label="Description"
            >{{ old('description', ($editing ? $categoryMaster->description :
            '')) }}</x-inputs.textarea
        >
    </x-inputs.group>
</div>
