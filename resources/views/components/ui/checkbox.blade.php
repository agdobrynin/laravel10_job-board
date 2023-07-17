{{-- Define compoment in App\View\Components\Ui\Checkbox.php --}}
@props([
    'type',
    'name',
    'value',
    'checked',
    'label',
    'showError'
])
<label {{ $attributes->class(['flex items-center font-semibold']) }}>
        <input type="{{ $type }}"
               @class(['border-red-500' => $showError && $errors->has($name)])
               @checked($checked)
               name="{{ $name }}"
               value="{{ $value }}"/>
        <span @class(['ms-2', 'text-red-500' => $showError && $errors->has($name)])>{{ $label }}</span>
</label>
@if($showError)
    @error($name)
    <div class="mt-1 text-xs text-red-500">
        {{ $message }}
    </div>
    @enderror
@endif
