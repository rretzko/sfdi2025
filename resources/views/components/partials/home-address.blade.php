<fieldset class="flex flex-col my-2 pt-2 border border-transparent border-t-gray-300">
    <label for="" class="font-semibold">Home Address</label>

    {{-- ADDRESS 1 --}}
    <div class="flex flex-col mb-2 ">
        <label for="form.address1">Address 1</label>
        <div class="flex flex-row space-x-2">
            <input type="text" wire:model.live.debounce.500ms="form.address1" class="max-w-md"/>
            {{-- SAVED MESSAGE --}}
            <div class="mt-6 ml-4">
                <x-action-message class="me-3 text-green-600 self-center" on="address1-updated">
                    {{ __('Saved.') }}
                </x-action-message>
            </div>
        </div>
        @error('address1')
        <div class="text-red-600">{{ $message }}</div>
        @enderror
    </div>

    {{-- ADDRESS 2 --}}
    <div class="flex flex-col mb-2 ">
        <label for="form.address2">Address 2</label>
        <div class="flex flex-row space-x-2">
            <input type="text" wire:model.live.debounce.500ms="form.address2" class="max-w-md"/>
            {{-- SAVED MESSAGE --}}
            <div class="mt-6 ml-4">
                <x-action-message class="me-3 text-green-600 self-center" on="address2-updated">
                    {{ __('Saved.') }}
                </x-action-message>
            </div>
        </div>
        @error('address2')
        <div class="text-red-600">{{ $message }}</div>
        @enderror
    </div>

    {{-- CITY, STATE POSTALCODE --}}
    <fieldset class="flex flex-row space-x-2 mb-2">

        {{-- CITY --}}
        <div class="flex flex-col">
            <label for="form.city">City</label>
            <input type="text" wire:model.live.debounce.500ms="form.city" class="max-w-md"/>
            {{-- SAVED MESSAGE --}}
            <div class="mt-6 ml-4">
                <x-action-message class="me-3 text-green-600 self-center" on="city-updated">
                    {{ __('Saved.') }}
                </x-action-message>
            </div>
            @error('city')
            <div class="text-red-600">{{ $message }}</div>
            @enderror
        </div>

        {{-- GEOSTATE ID --}}
        <div class="flex flex-col mb-2 ">
            <label for="form.geostateId">State</label>
            <select wire:model.live="form.geostateId" class="w-fit">
                @foreach($geostates AS $id => $name)
                    <option value="{{ $id }}">
                        {{ $name }}
                    </option>
                @endforeach
            </select>
            {{-- SAVED MESSAGE --}}
            <div class="mt-6 ml-4">
                <x-action-message class="me-3 text-green-600 self-center" on="geostate-id-updated">
                    {{ __('Saved.') }}
                </x-action-message>
            </div>
            @error('geostateId')
            <div class="text-red-600">{{ $message }}</div>
            @enderror
        </div>

        {{-- POSTAL CODE --}}
        <div class="flex flex-col">
            <label for="form.postalCode">Zip Code</label>
            <input type="text" wire:model.live.debounce.500ms="form.postalCode" class="max-w-sm"/>
            {{-- SAVED MESSAGE --}}
            <div class="mt-6 ml-4">
                <x-action-message class="me-3 text-green-600 self-center" on="postal-code-updated">
                    {{ __('Saved.') }}
                </x-action-message>
            </div>
            @error('postalCode')
            <div class="text-red-600">{{ $message }}</div>
            @enderror
        </div>
    </fieldset>

</fieldset>
