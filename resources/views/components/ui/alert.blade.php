@props([
    'header'
])
<div
    {{ $attributes->class(['relative mb-4 rounded-md border p-4 pr-10 border-gray-300 bg-gray-100 text-gray-700']) }}
    x-data="{closed: false}"
    :class="closed ? 'hidden' : ''"
    >
    <button
        type="button"
        class="absolute top-4 right-0 flex h-full pr-4 h-6 w-10"
        x-on:click="closed = true">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
             stroke="currentColor" class="h-6 w-6 text-slate-600">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
        </svg>
    </button>
    @if($header)
        <h1 {{ $header->attributes->class(['text-xl mb-4']) }}>
            {{ $header }}
        </h1>
    @endif
    {{ $slot }}
</div>
