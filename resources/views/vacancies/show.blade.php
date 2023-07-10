<x-layouts.app :pageTitle="$vacancy['title']">
    <x-ui.breadcrumbs
        class="mb-4"
        :links="['Home' => '/', 'Vacancies' => route('vacancies.index'), $vacancy['title'] => null]"/>
    <x-vacancy.card :$vacancy>
        <x-slot:description>
            {!! nl2br(e($vacancy->description)) !!}
        </x-slot:description>
    </x-vacancy.card>
</x-layouts.app>
