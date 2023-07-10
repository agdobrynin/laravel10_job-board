{{-- Define component in App\View\Components\Ui\GroupBox.php --}}
@props([
    'legend',
    'name',
    'options',
    'value'
])
<fieldset {{ $attributes->class(['border rounded-md p-4', 'border-red-500' => $errors->has($name)]) }}>
    <legend class="px-2 font-semibold">{{ $legend }}</legend>
    <x-ui.checkbox
        name="{{ $name }}"
        :checked="true"
        class="mb-2"
        label="All"/>
    @foreach($options as $option)
        <x-ui.checkbox
            name="{{ $name }}"
            class="mb-2"
            :checked="$value === $option->value"
            :value="$option->value"
            :label="$option->label()"/>
    @endforeach
    @error($name)
    <div class="mt-1 text-xs text-red-500">
        <p>Choose value: {{ \Illuminate\Support\Str::ucfirst(old($name)) }}</p>
        {{ $message }}
    </div>
    @enderror
</fieldset>
