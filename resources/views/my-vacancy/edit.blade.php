<x-layouts.app pageTitle="Create Vacancy">
    <x-ui.breadcrumbs
        class="mb-4"
        :links="[
            'Vacancies' => route('vacancies.index'),
            $vacancy->employer->name .' vacancies' => route('my-vacancy.index'),
            'Update vacancy' => null]"/>

    <x-ui.card>
        <x-vacancy.form
            :$vacancy
            method="put"
            action="{{ route('my-vacancy.update', $vacancy) }}"
            buttonTitle="Update"
        />
    </x-ui.card>
</x-layouts.app>
