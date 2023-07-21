@props(['type' => 'button', 'disabled' => false])
<button
    type="{{$type}}"
    @disabled($disabled)
    {{ $attributes->class([
        'rounded-md border border-slate-300 bg-white px-2.5 py-1.5 text-center text-sm font-semibold text-black shadow-sm hover:bg-slate-100',
        'text-slate-300 cursor-not-allowed hover:shadow-none' => $disabled
        ]) }}>
    {{ $slot }}
</button>
