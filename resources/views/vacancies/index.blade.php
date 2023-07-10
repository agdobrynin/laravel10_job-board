<x-layouts.app pageTitle="Vacancies list">
    <x-ui.breadcrumbs
        class="mb-4"
        :links="['Home' => '/', 'Vacancies' => null]"/>

    <x-ui.card class="mb-4 text-sm">
        <form action="{{ route('vacancies.index') }}" method="get">
            <div class="mb-4 grid grid-cols-2 gap-4">
                <div>
                    <x-ui.input name="search" label="Search" :value="old('search', request('search'))"
                                placeholder="Search text"/>
                </div>
                <div>
                    <div class="font-semibold mb-1">Salary</div>
                    <div class="flex space-x-2">
                        <x-ui.input type="number" name="salary_min" :value="old('salary_min', request('salary_min'))"
                                    placeholder="From"/>
                        <x-ui.input type="number" name="salary_max" :value="old('salary_max', request('salary_max'))"
                                    placeholder="To"/>
                    </div>
                </div>
                <div>
                    <x-ui.group-box
                        legend="Experience"
                        name="experience"
                        :value="request('experience', '')"
                        :options="App\Enums\VacancyExperienceEnum::cases()"
                        class="h-full"
                    />
                </div>
                <div>
                    <x-ui.group-box
                        legend="Category"
                        name="category"
                        :value="request('category', '')"
                        :options="App\Enums\VacancyCategoryEnum::cases()"
                        class="h-full"
                    />
                </div>
            </div>
            <x-ui.button class="w-full">Filter</x-ui.button>
        </form>
    </x-ui.card>

    @forelse($vacancies as $vacancy)
        <x-vacancy.card class="mb-4" :$vacancy>
            <x-slot:footer class="pt-4">
                <x-ui.link-button href="{{ route('vacancies.show', $vacancy) }}">
                    Show
                </x-ui.link-button>
            </x-slot:footer>
        </x-vacancy.card>
    @empty
        <x-ui.card class="text-slate-400 font-semibold text-center">
            Not found vacancies
        </x-ui.card>
    @endforelse
</x-layouts.app>