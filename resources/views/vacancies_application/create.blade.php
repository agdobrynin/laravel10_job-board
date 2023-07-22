<x-layouts.app :pageTitle="'Application for vacancy'.$vacancy->title">
    <x-ui.breadcrumbs
        class="mb-4"
        :links="[
            'Vacancies' => route('vacancies.index'),
            $vacancy['title'] => route('vacancies.show', $vacancy),
            'Apply' => null,
        ]"/>

    <x-vacancy.card class="mb-4" :$vacancy />

    <x-ui.card>
        <h2 class="mb-4 text-lg font-medium">
            Your Job Application
        </h2>

        <form action="{{ route('vacancies.application.store', $vacancy) }}" method="post"
            enctype="multipart/form-data">
            @csrf
            <div class="mb-4">
                <x-ui.input name="expect_salary"
                            :required="true"
                            value="{{ old('expect_salary', '') }}"
                            label="Expected Salary"/>
            </div>
            <div class="mb-4">
                <x-ui.input name="cv"
                            type="file"
                            :required="true"
                            label="Your CV latter (format: pdf, docx, doc, odt, txt)"/>
            </div>

            <x-ui.button type="submit" class="w-full mt-4">Apply</x-ui.button>
        </form>
    </x-ui.card>
</x-layouts.app>
