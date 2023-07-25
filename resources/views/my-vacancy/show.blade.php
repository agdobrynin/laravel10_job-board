<x-layouts.app pageTitle="View vacancy applications">
    <x-ui.breadcrumbs
        class="mb-4"
        :links="[
            'Vacancies' => route('vacancies.index'),
            $vacancy->employer->name .' vacancies' => route('my-vacancy.index'),
            'Vacancy applications' => null]"/>

    <x-vacancy.card :$vacancy class="mb-4">
        <x-slot:footer>
            <div class="flex justify-between">
                <form action="{{ route('my-vacancy.destroy', $vacancy) }}" method="post">
                    @csrf
                    @method('delete')
                    <x-ui.button type="submit" class="text-red-500">Archive vacancy</x-ui.button>
                </form>

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
        </x-slot:footer>
    </x-vacancy.card>

    @if($applications->hasPages())
        <div class="mb-4">
            {{ $applications->onEachSide(1)->links() }}
        </div>
    @endif

    @forelse($applications as $application)
        <x-ui.card class="mb-4">
            <div class="mb-4 flex items-center justify-between">
                <div>
                    <div class="font-semibold">{{ $application->user->name }}</div>
                    <div>
                        📧 <a href="mailto:{{ $application->user->email }}" class="link">{{ $application->user->email }}</a>
                    </div>
                    <div>
                        ⏰ Applied {{ $application->created_at->diffForHumans() }}
                    </div>
                    @if($application->cv_path)
                        <div>
                            📃 <a class="link"
                                 href="{{ route('my-vacancy.download', [$vacancy, $application]) }}"
                                 target="_blank">Download CV</a>
                        </div>
                    @endif
                </div>

                <div class="text-2xl">${{ number_format($application->expect_salary) }}</div>
            </div>
        </x-ui.card>
    @empty
        <x-ui.card>Not found applications yet.</x-ui.card>
    @endforelse

    @if($applications->hasPages())
        <div>
            {{ $applications->onEachSide(1)->links() }}
        </div>
    @endif
</x-layouts.app>
