<x-layouts.app pageTitle="Create Vacancy">
    <x-ui.breadcrumbs
        class="mb-4"
        :links="['Vacancies' => route('vacancies.index'), 'Create vacancy' => null]"/>

    <x-ui.card>
        <x-vacancy.form
            :vacancy="null"
            method="post"
            action="{{ route('my-vacancy.store') }}"
            buttonTitle="Create"
        />
    </x-ui.card>
</x-layouts.app>
