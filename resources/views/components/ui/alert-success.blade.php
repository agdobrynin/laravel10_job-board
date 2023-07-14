@props([
    'header'
])
<x-ui.alert {{$attributes->class(['border-green-300 !bg-green-100 text-green-700'])}}>
    @if($header)
        <x-slot:header>{{ $header }}</x-slot:header>
    @endif
    {{ $slot }}
</x-ui.alert>
