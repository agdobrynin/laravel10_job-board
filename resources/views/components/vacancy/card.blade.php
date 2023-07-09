@props([
    'vacancy',
    /*Named slot footer*/
    'footer' => null,
])
<x-ui.card {{ $attributes->class('') }}>
    <div class="flex justify-between">
        <h2 class="text-lg font-medium">{{ $vacancy->title }}</h2>
        <div class="text-slate-500">Salary: ${{ number_format($vacancy->salary) }}</div>
    </div>
    <div class="mb-4 flex items-center justify-between text-sm text-slate-500">
        <div class="flex space-x-4">
            <div>Company name</div>
            <div>{{ $vacancy->location }}</div>
        </div>
        <div class="flex space-x-1 text-xs">
            <x-ui.tag>{{ Str::ucfirst($vacancy->experience) }}</x-ui.tag>
            <x-ui.tag>{{ Str::upper($vacancy->category) }}</x-ui.tag>
        </div>
    </div>
    <p class="mb-4 text-sm text-slate-500">{!! nl2br(e($vacancy->description)) !!}</p>
    @if($footer)
        <div {{ $footer->attributes->class(['']) }}>
            {{ $footer }}
        </div>
    @endif
</x-ui.card>
