@props([
    'header'
])
<x-ui.alert {{$attributes->class(['border-red-300 !bg-red-100 text-red-700'])}}>
    @if($header)
        <x-slot:header>{{ $header }}</x-slot:header>
    @endif
    {{ $slot }}
</x-ui.alert>
