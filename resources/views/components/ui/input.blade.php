@props([
    'name',
    'value' => '',
    'placeholder' => '',
    'label' => '',
    'type' => 'text',
    'clearBtn' => true,
])
<label
    x-init="val = '{{$value}}'; hasError = Boolean({{ $errors->has($name) }})"
    x-data="{
        val: '',
        hasError: false,
        clear() {
            this.$refs.input.value = '';
        },
        submit() {
            this.$refs.input.form?.submit();
        },
    }"
>
    @if($label)<div class="font-semibold mb-1 ms-1">{{ $label }}</div>@endif
    <div class="relative">
        @if($clearBtn)
            <button
                x-show="val.length"
                type="button"
                class="absolute top-0 right-0 flex h-full items-center pr-2"
                x-on:click="clear(); submit();">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                     stroke="currentColor" class="h-4 w-4 text-slate-500">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        @endif
        <input type="{{ $type }}"
               x-ref="input"
               x-model="val"
               x-on:input="hasError = false"
               :class="{ '!border-red-300 text-red-600': hasError }"
               {{ $attributes->class([
                   'form-input rounded-md border-slate-200 w-full',
                   '!border-red-300 text-red-600' => $errors->has($name),
                   'pr-6' => $clearBtn,
               ]) }}
               name="{{ $name }}"
               value="{{ $value }}"
               placeholder="{{ $placeholder }}" />
    </div>
    @error($name)
        <div x-show="hasError" class="mt-1 text-xs text-red-500">
            {{ $message }}
        </div>
    @enderror
</label>
