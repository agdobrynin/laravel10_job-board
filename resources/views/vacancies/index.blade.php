<x-layouts.app pageTitle="Vacancies list">
    <x-ui.breadcrumbs
        class="mb-4"
        :links="['Home' => '/', 'Vacancies' => null]"/>
    @foreach($vacancies as $vacancy)
        <x-vacancy.card class="mb-4" :$vacancy>
            <x-slot:footer class="border-t border-slate-200 pt-4">
                <x-ui.link-button href="{{ route('vacancies.show', $vacancy) }}">
                    Show
                </x-ui.link-button>
            </x-slot:footer>
        </x-vacancy.card>
    @endforeach
</x-layouts.app>
