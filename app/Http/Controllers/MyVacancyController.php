<?php

namespace App\Http\Controllers;

use App\Http\Requests\VacancyRequest;
use App\Models\Employer;
use App\Models\Vacancy;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class MyVacancyController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Vacancy::class, 'my_vacancy');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $perPage = config('app.paginator.vacancies.list');

        $vacancies = auth()->user()->employer
            ->vacancies()
            ->with('employer')
            ->withCount('vacancyApplications')
            ->orderBy('vacancy_applications_count', 'desc')
            ->latest()
            ->paginate($perPage);

        return view('my-vacancy.index', compact('vacancies'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('my-vacancy.create');
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(VacancyRequest $request): RedirectResponse
    {
        /** @var Employer $employer */
        $employer = $request->user()->employer;
        $vacancy = Vacancy::make($request->validated());
        $employer->vacancies()->save($vacancy);

        return to_route('my-vacancy.index')
            ->with('success', 'Vacancy was created');
    }


    /**
     * Display the specified resource.
     */
    public function show(Vacancy $myVacancy): View
    {
        $perPage = config('app.paginator.vacancies.employer.applications');

        $myVacancy->loadMissing(['employer']);

        $applications = $myVacancy->vacancyApplications()
            ->with(['user'])
            ->orderBy('expect_salary')
            ->latest()
            ->paginate($perPage);

        return view('my-vacancy.show', ['vacancy' => $myVacancy, 'applications' => $applications]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Vacancy $myVacancy): View
    {
        return view('my-vacancy.edit', ['vacancy' => $myVacancy]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(VacancyRequest $request, Vacancy $myVacancy): RedirectResponse
    {
        $myVacancy->update($request->validated());

        return back()->with('success', 'Vacancy was updated');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Vacancy $myVacancy): RedirectResponse
    {
        $myVacancy->delete();

        return to_route('my-vacancy.index')->with('success', 'Vacancy was deleted');
    }
}
