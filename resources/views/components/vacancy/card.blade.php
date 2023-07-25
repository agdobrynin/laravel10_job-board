@props([
    'vacancy',
    /*Named slot description*/
    'description' => null,
    /*Named slot footer*/
    'footer' => null,
])
<x-ui.card {{ $attributes->class(['']) }}>
    @if($vacancy->deleted_at)
        <del>
    @endif
    <div class="flex justify-between">
        <h2 class="text-lg font-medium">{{ $vacancy->title }}</h2>
        <div class="text-slate-500">Salary: ${{ number_format($vacancy->salary) }}</div>
    </div>
    <div class="mb-4 flex items-center justify-between text-sm text-slate-500">
        <div class="flex space-x-4">
            <div>Employer: <span class="font-semibold text-emerald-500">{{ $vacancy->employer->name }}</span></div>
            <div>Location: <span class="font-semibold text-emerald-500">{{ $vacancy->location }}</span></div>
        </div>
        <div class="flex space-x-1 text-xs">
            <x-ui.tag class="hover:text-red-600 hover:shadow">
                <a href="{{ route('vacancies.index', [...request()->query(), 'experience' => $vacancy->experience]) }}">
                    {{ Str::upper($vacancy->experience) }}
                </a>
            </x-ui.tag>
            <x-ui.tag class="hover:text-indigo-600 hover:shadow">
                <a href="{{ route('vacancies.index', [...request()->query(), 'category' => $vacancy->category]) }}">
                    {{ Str::upper($vacancy->category) }}
                </a>
            </x-ui.tag>
        </div>
    </div>
    @if($vacancy->deleted_at)
        </del>
    @endif
    @if($description)
        <p {{ $description->attributes->class(['mb-4 text-sm text-slate-500']) }}>{{ $description }}</p>
    @endif
    @if($footer)
        <div {{ $footer->attributes->class(['']) }}>
            {{ $footer }}
        </div>
    @endif
</x-ui.card>
