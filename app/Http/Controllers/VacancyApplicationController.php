<?php

namespace App\Http\Controllers;

use App\Http\Requests\VacancyApplicationStoreRequest;
use App\Models\Vacancy;
use App\Models\VacancyApplication;
use App\Notifications\OfferFromEmployee;

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
        /** @var VacancyApplication $application */
        $application = $vacancy->vacancyApplications()->make($request->validated());
        $application->user()
            ->associate($request->user())
            ->save();

        $vacancy->employer
            ->user
            ->notify(new OfferFromEmployee($application));

        return redirect()->route('vacancies.show', $vacancy)
            ->with('success', 'You apply to this vacancy.');
    }
}
