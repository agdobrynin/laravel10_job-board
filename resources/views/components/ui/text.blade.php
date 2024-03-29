@props([
    'name',
    'text' => '',
    'placeholder' => '',
    'label' => '',
    'clearBtn' => true,
    'clearAndSubmit' => false,
    'id' => Str::uuid(),
    'required' => false,
])
<div
    x-init="val = '{{ str_replace(["\r","\n"], ["", "\\n"], $text)}}'; hasError = Boolean({{ $errors->has($name) }}); clearAndSubmit = Boolean({{ $clearAndSubmit }})"
    x-data="{
        clearAndSubmit: false,
        val: '',
        hasError: false,
        clear(withSubmit) {
            this.val = '';
            this.hasError = false;

            if (withSubmit) {
                this.$refs.input.value = '';
                this.$refs.input.form?.submit();
            }
        }
    }">
    @if($label)
        <label class="font-semibold mb-2 ms-1 block cursor-pointer" for="{{$id}}">{{ $label }}</label>
    @endif
    <div class="relative">
        @if($clearBtn)
            <button
                x-show="val.length"
                type="button"
                class="absolute top-1 right-4 flex"
                x-on:click="clear(clearAndSubmit)">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                     stroke="currentColor" class="h-4 w-4 text-slate-500">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        @endif
        <textarea
            id="{{$id}}"
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
            @required($required)
            placeholder="{{ $placeholder }}"
        ></textarea>
    </div>
    @error($name)
        <div x-show="hasError" class="mt-1 text-xs text-red-500">
            {{ $message }}
        </div>
    @enderror
</div>
