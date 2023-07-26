<?php

namespace App\Http\Controllers;

use App\Contracts\VacancyApplicationCvStorageInterface;
use App\Dto\VacancyApplicationStoreDto;
use App\Http\Requests\VacancyApplicationStoreRequest;
use App\Models\Vacancy;
use App\Models\VacancyApplication;
use App\Notifications\OfferFromEmployee;

class VacancyApplicationController extends Controller
{
    public function store(
        VacancyApplicationStoreRequest       $request,
        Vacancy                              $vacancy,
        VacancyApplicationCvStorageInterface $cvStorage,
    )
    {
        $this->authorize('apply', $vacancy);

        $dto = new VacancyApplicationStoreDto(...$request->validated());

        $cvPath = $cvStorage->adapter()->putFile($dto->cv);

        /** @var VacancyApplication $application */
        $application = $vacancy->vacancyApplications()
            ->create([
                'expect_salary' => $dto->expect_salary,
                'cv_path' => $cvPath,
                'user_id' => $request->user()->id,
            ]);

        $vacancy->employer
            ->user
            ->notify(new OfferFromEmployee($application));

        return redirect()->route('vacancies.show', $vacancy)
            ->with('success', 'You apply to this vacancy.');
    }

    public function create(Vacancy $vacancy)
    {
        $this->authorize('apply', $vacancy);

        return view('vacancies_application.create', ['vacancy' => $vacancy]);
    }
}
