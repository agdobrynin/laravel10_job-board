<x-layouts.app :pageTitle="$vacancy['title']">
    <x-ui.breadcrumbs
        class="mb-4"
        :links="['Home' => '/', 'Vacancies' => route('vacancies.index'), $vacancy['title'] => null]"/>
    <x-vacancy.card :$vacancy>
        <x-slot:description>
            {!! nl2br(e($vacancy->description)) !!}
        </x-slot:description>
    </x-vacancy.card>

    @if($vacancy->employer->vacancies->count())
        <x-ui.card class="mt-4">
            <h2 class="text-2xl mb-4">Other vacancies from &laquo;{{ $vacancy->employer->name }}&raquo;</h2>
            <div class="text-sm text-slate-500">
                @foreach($vacancy->employer->vacancies as $otherVacancy)
                    <div class="mb-4 flex justify-between">
                        <div>
                            <div class="text-slate-700">
                                <a href="{{ route('vacancies.show', $otherVacancy) }}" class="link font-semibold">
                                    {{ $otherVacancy->title }}
                                </a>
                            </div>
                            <div class="text-xs">
                                {{ $otherVacancy->created_at->diffForHumans() }}
                            </div>
                        </div>
                        <div>${{ number_format($otherVacancy->salary) }}</div>
                    </div>
                @endforeach
            </div>
        </x-ui.card>
    @endif
</x-layouts.app>
