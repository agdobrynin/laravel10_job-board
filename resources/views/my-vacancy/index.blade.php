<x-layouts.app pageTitle="My vacancies with applications">
    <x-ui.breadcrumbs
        class="mb-4"
        :links="['Vacancies' => route('vacancies.index'), 'Vacancies of '.auth()->user()->employer->name => null]"/>

    @if($vacancies->hasPages())
        <div class="mb-4">
            {{ $vacancies->onEachSide(1)->links() }}
        </div>
    @endif

    @forelse($vacancies as $vacancy)
        <x-vacancy.card class="mb-4" :$vacancy>
            <x-slot:description>
                Vacancy was created {{ $vacancy->created_at->diffForHumans() }}
                and has <span class="font-semibold">{{ $vacancy->vacancy_applications_count }}
                    {{ Str::plural('application', $vacancy->vacancy_applications_count) }}</span>
            </x-slot:description>
            <x-slot:footer class="pt-4">
                <div class="sm:grid sm:grid-cols-1 md:flex md:justify-between">
                    <div>
                        <x-ui.link-button
                            class="text-indigo-600"
                            :disable="!$vacancy->vacancy_applications_count || $vacancy->deleted_at"
                            href="{{ route('my-vacancy.show', $vacancy) }}">
                            View applications
                        </x-ui.link-button>
                    </div>
                    <div>
                        <x-ui.link-button
                            class="text-green-600"
                            :disable="$vacancy->vacancy_applications_count || $vacancy->deleted_at"
                            href="{{ route('my-vacancy.edit', $vacancy) }}">
                            Edit vacancy
                        </x-ui.link-button>
                    </div>
                    <div>
                        @if ($vacancy->deleted_at)
                            <x-ui.link-button
                                class="text-yellow-500"
                                href="{{ route('my-vacancy.restore', $vacancy) }}">
                                Restore vacancy
                            </x-ui.link-button>
                        @else
                            <form action="{{ route('my-vacancy.destroy', $vacancy) }}" method="post">
                                @csrf
                                @method('delete')
                                <x-ui.button
                                    type="submit"
                                    class="text-red-500">
                                    Archive vacancy
                                </x-ui.button>
                            </form>
                        @endif
                    </div>
                    <div>
                        <form action="{{ route('my-vacancy.force_destroy', $vacancy) }}" method="post">
                            @csrf
                            @method('delete')
                            <x-ui.button
                                type="submit"
                                class="text-fuchsia-700">
                                Permanent delete
                            </x-ui.button>
                        </form>
                    </div>
                </div>
            </x-slot:footer>
        </x-vacancy.card>
    @empty
        <x-ui.card class="text-lg text-center">
            Vacancies not found. Add <a href="{{ route('my-vacancy.create') }}" class="link">new vacancy here</a>
        </x-ui.card>
    @endforelse

    @if($vacancies->hasPages())
        <div class="mb-4">
            {{ $vacancies->onEachSide(1)->links() }}
        </div>
    @endif
</x-layouts.app>
