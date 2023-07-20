<?php

namespace App\Http\Controllers;

use App\Models\VacancyApplication;

class MyVacancyApplicationController extends Controller
{
    public function index()
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

    public function destroy(VacancyApplication $myVacancyApplication)
    {
        abort_if(
            $myVacancyApplication->user_id !== auth()->user()?->id,
            403,
            'You are not owner this vacancy application'
        );

        $myVacancyApplication->delete();

        return back()->with('success', 'Your application remove');
    }
}
