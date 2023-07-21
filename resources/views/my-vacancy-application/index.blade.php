<x-layouts.app pageTitle="My vacancies applications">
    <x-ui.breadcrumbs
        class="mb-4"
        :links="['Vacancies' => route('vacancies.index'), 'My vacancies applications' => null]"/>

    @if($vacancyApplications->hasPages())
        <div class="mb-4">
            {{ $vacancyApplications->links() }}
        </div>
    @endif

    @forelse($vacancyApplications as $application)
        <x-vacancy.card :vacancy="$application->vacancy" class="mb-4">
            <x-slot:description>
                Employer contact: <span class="font-semibold">{{ $application->vacancy->employer->user->email }}</span>
            </x-slot:description>
            <x-slot:footer>
                <div class="flex justify-between items-center">
                    <div class="text-slate-500 text-xs grid gap-6 xs:grid-cols-1 sm:grid-cols-2">
                        <div>
                            Your applied
                            <span class="font-semibold">{{ $application->created_at->diffForHumans() }}</span>
                        </div>
                        <div>
                            Your asking salary
                            <span class="font-semibold">${{ number_format($application->expect_salary) }}</span>
                        </div>
                        <div>
                            Average asking salary
                            <span class="font-semibold">${{ number_format($application->vacancy->vacancy_applications_avg_expect_salary) }}</span>
                        </div>
                        <div>
                            <span class="font-semibold">{{ $application->vacancy->vacancy_applications_count - 1 }}</span>
                            {{ Str::plural('applicant', $application->vacancy->vacancy_applications_count - 1) }} for this vacancy
                        </div>
                    </div>
                    <div>
                        <form action="{{ route('my-vacancy-applications.destroy', $application) }}" method="post">
                            @csrf
                            @method('delete')
                            <x-ui.button type="submit">
                                ❌ Cancel my application
                            </x-ui.button>
                        </form>
                        <x-ui.link-button class="block mt-4" href="{{ route('vacancies.show', $application->vacancy) }}">
                            🔍 View vacancy details
                        </x-ui.link-button>
                    </div>
                </div>
            </x-slot:footer>
        </x-vacancy.card>
    @empty
        <x-ui.card>
            <h3 class="mb-4 text-xl text-center text-slate-700">Applications to vacancies not found.</h3>
            <div class="text-center">
                Go find some vacancies <a class="link" href="{{ route('vacancies.index') }}">here!</a>
            </div>
        </x-ui.card>
    @endforelse

    @if($vacancyApplications->hasPages())
        <div class="mb-4">
            {{ $vacancyApplications->links() }}
        </div>
    @endif

</x-layouts.app>
