<x-layouts.app :pageTitle="$vacancy['title']">
    <x-ui.breadcrumbs
        class="mb-4"
        :links="['Home' => '/', 'Vacancies' => route('vacancies.index'), $vacancy['title'] => null]"/>
    <x-vacancy.card :$vacancy />
</x-layouts.app>
