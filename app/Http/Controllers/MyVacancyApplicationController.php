<?php

namespace App\Http\Controllers;

use App\Contracts\VacancyApplicationCvStorageInterface;
use App\Models\VacancyApplication;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MyVacancyApplicationController extends Controller
{
    public function index(): View
    {
        $perPage = config('app.paginator.my_vacancy_applications.list');

        $vacancyApplications = auth()->user()
            ->vacancyApplications()
            ->with(['vacancy' => function ($vacancy) {
                $vacancy->withCount('vacancyApplications')
                    ->withAvg('vacancyApplications', 'expect_salary');
            }, 'vacancy.employer.user'])
            ->latest()
            ->paginate($perPage);

        return view('my-vacancy-application.index', compact('vacancyApplications'));
    }

    public function destroy(VacancyApplication $myVacancyApplication): RedirectResponse
    {
        $this->authorize('delete', $myVacancyApplication);

        $myVacancyApplication->forceDelete();

        return back()->with('success', 'Your application remove');
    }

    public function download(VacancyApplication $myVacancyApplication, VacancyApplicationCvStorageInterface $cvStorage): StreamedResponse
    {
        $this->authorize('view', $myVacancyApplication);

        return $cvStorage->adapter()->download($myVacancyApplication->cv_path);
    }
}
