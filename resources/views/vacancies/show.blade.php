<x-layouts.app :pageTitle="$vacancy['title']">
    <x-ui.breadcrumbs
        class="mb-4"
        :links="['Vacancies' => route('vacancies.index'), $vacancy['title'] => null]"/>
    <x-vacancy.card :$vacancy>
        <x-slot:description>
            {!! nl2br(e($vacancy->description)) !!}
        </x-slot:description>

        <x-slot:footer>
            @cannot('apply', $vacancy)
                <div class="text-center text-sm font-medium text-slate-500">
                    You already applied to this vacancy
                </div>
            @else
                <div class="mt-6 text-end">
                    <x-ui.link-button :href="route('vacancies.application.create', $vacancy)">
                        📝 Offer yourself
                    </x-ui.link-button>
                </div>
            @endcan
        </x-slot:footer>
    </x-vacancy.card>

    @if($otherVacancies->count())
        <x-ui.card class="mt-4">
            <h2 class="text-2xl mb-4">Other vacancies from &laquo;{{ $vacancy->employer->name }}&raquo;</h2>
            <div class="text-sm text-slate-500">

                @if($otherVacancies->hasPages())
                    <div class="mb-4">
                        {{ $otherVacancies->onEachSide(1)->links() }}
                    </div>
                @endif

                @foreach($otherVacancies as $otherVacancy)
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

                @if($otherVacancies->hasPages())
                    <div class="mb-4">
                        {{ $otherVacancies->onEachSide(1)->links() }}
                    </div>
                @endif
            </div>
        </x-ui.card>
    @endif
</x-layouts.app>
