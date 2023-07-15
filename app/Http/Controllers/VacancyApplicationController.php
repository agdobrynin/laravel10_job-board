<?php

namespace App\Http\Controllers;

use App\Http\Requests\VacancyApplicationStoreRequest;
use App\Models\Vacancy;
use App\Models\VacancyApplication;

class VacancyApplicationController extends Controller
{
    public function create(Vacancy $vacancy)
    {
        $this->authorize('apply', $vacancy);

        return view('vacancies_application.create', ['vacancy' => $vacancy]);
    }

    public function store(VacancyApplicationStoreRequest $request, Vacancy $vacancy)
    {
        $this->authorize('apply', $vacancy);

        $vacancy->vacancyApplications()->make($request->validated())
            ->user()
            ->associate($request->user())
            ->save();

        return redirect()->route('vacancies.show', $vacancy)
            ->with('success', 'You apply to this vacancy.');
    }


    public function destroy(string $vacancyUuid, VacancyApplication $application)
    {
        $application->delete();

        return back()->with('success', 'Vacancy application was deleted.');
    }
}
