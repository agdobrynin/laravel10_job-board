@props([
    'href' => '',
])
<a {{ $attributes->class(['rounded-md border border-slate-300 bg-white px-2.5 py-1.5 text-center text-sm font-semibold text-black shadow-sm hover:shadow-md hover:bg-slate-100']) }}
   href="{{ $href }}">{{ $slot }}</a>
