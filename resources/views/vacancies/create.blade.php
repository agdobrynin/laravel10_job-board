<x-layouts.app pageTitle="Create Vacancy">
    <x-ui.breadcrumbs
        class="mb-4"
        :links="['Vacancies' => route('vacancies.index'), 'Create vacancy' => null]"/>

    <x-ui.card>
        <form action="{{ route('vacancies.store') }}" method="post">
            @csrf
            <div class="mb-4">
                <x-ui.input name="title"
                            label="Vacancy title"
                            :required="true"
                            value="{{ old('title') }}"/>
            </div>
            <div class="mb-4">
                <x-ui.text name="description"
                           label="Vacancy description"
                           :required="true"
                           value="{{ old('description') }}"
                           rows="5"/>
            </div>
            <div class="grid md:grid-cols-2 sm:grid-cols-1 gap-4 mb-4">
                <div>
                    <x-ui.input name="salary"
                                label="Salary ($)"
                                :required="true"
                                value="{{ old('salary') }}"/>
                </div>
                <div>
                    <x-ui.input name="location"
                                label="Vacancy location (city)"
                                :required="true"
                                value="{{ old('location') }}"/>
                </div>
            </div>
            <div class="grid md:grid-cols-2 sm:grid-cols-1 gap-4 mb-4">
                <div>
                    <x-ui.group-box
                        legend="Experience"
                        name="experience"
                        :required="true"
                        :value="old('experience', '')"
                        :withAll="false"
                        :options="App\Enums\VacancyExperienceEnum::cases()"
                        class="h-full"
                    />
                </div>
                <div>
                    <x-ui.group-box
                        legend="Category"
                        name="category"
                        :required="true"
                        :value="old('category', '')"
                        :withAll="false"
                        :options="App\Enums\VacancyCategoryEnum::cases()"
                        class="h-full"
                    />
                </div>
            </div>
            <x-ui.button class="w-full" type="submit">Create</x-ui.button>
        </form>
    </x-ui.card>
</x-layouts.app>
