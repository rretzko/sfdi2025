@props([
    'accept' => 'audio/mpeg, audio/ogg, audio/wav',
    'autofocus' => false,
    'blur' => true,
    'error' => '',
    'hint' => '',
    'label',
    'name',
    'placeholder' => '',
    'required' => false,
    'results' => '',
    'type' => 'text',
])
<div class="flex flex-col">
    <label for="{{ $name }}" class="@if($required) required @endif">{{ ucwords($label) }}</label>
    <input type="file"
           id="{{ $name }}"
           name="{{ $name }}"
           accept="{{ $accept }}"
           @class([
            'wide',
            'border border-red-600' => $errors->has($name),
            ])
           @if($blur)wire:model.blur @else
               wire:model
           @endif ="{{ $name }}"
           placeholder="{{ $placeholder }}"
           @required($required)
           @if($autofocus) autofocus @endif
           aria-label="{{ $label }}"
           @error($name)
           aria-invalid="true"
           aria-description="{{ $message }}"
        @enderror
    />

    {{-- HINT --}}
    @if($hint)
        <div class="text-xs ml-1 italic">
            {!! $hint !!}
        </div>
    @endif

    {{-- RESULTS --}}
    <div class="flex flex-col"> {{--  bg-gray-100 w-1/3 mt-2 --}}
        {!! $results !!}
    </div>

    {{-- ERROR --}}
    @error($name)
    <x-input-error messages="{{ $message }}" aria-live="polite"/>
    @enderror

</div>
