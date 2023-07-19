<?php

namespace App\Http\Controllers;

use App\Dto\FilterVacancyDto;
use App\Http\Requests\VacanciesIndexRequest;
use App\Models\Vacancy;

class VacancyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(VacanciesIndexRequest $request)
    {
        $dto = new FilterVacancyDto(...$request->validated());
        $perPage = config('app.paginator.vacancies.list');

        $vacancies = Vacancy::with('employer')
            ->filter($dto)
            ->latest()
            ->paginate($perPage)
            ->withQueryString();

        return view('vacancies.index', ['vacancies' => $vacancies]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Vacancy $vacancy)
    {
        $perPage = config('app.paginator.vacancies.employer.vacancies');

        $vacancy->loadMissing('employer');

        $otherVacancies = $vacancy->relatedVacancies()
            ->latest()
            ->paginate($perPage);

        return view('vacancies.show', compact(['vacancy', 'otherVacancies']));
    }
}
