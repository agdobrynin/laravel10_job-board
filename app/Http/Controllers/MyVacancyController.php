<?php

namespace App\Http\Controllers;

use App\Contracts\VacancyApplicationCvStorageInterface;
use App\Http\Requests\VacancyRequest;
use App\Models\Employer;
use App\Models\Vacancy;
use App\Models\VacancyApplication;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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
            ->withTrashed()
            ->with('employer')
            ->withCount([
                'vacancyApplications' => fn(Builder $q) => $q->withTrashed()])
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

        return to_route('my-vacancy.index')->with('success', 'Vacancy was archived');
    }

    /**
     * Remove the specified resource from storage.
     * @throws AuthorizationException
     */
    public function forceDestroy(Vacancy $myVacancy): RedirectResponse
    {
        $this->authorize('forceDelete', $myVacancy);

        $myVacancy->forceDelete();

        return to_route('my-vacancy.index')->with('success', 'Vacancy was permanent deleted');
    }

    /**
     * Restore the specified resource from storage.
     */
    public function restore(Vacancy $myVacancy): RedirectResponse
    {
        $this->authorize('restore', $myVacancy);

        $myVacancy->restore();

        return to_route('my-vacancy.index')->with('success', 'Vacancy was restored');
    }

    public function download(
        Vacancy                              $myVacancy,
        VacancyApplication                   $vacancyApplication,
        VacancyApplicationCvStorageInterface $cvStorage,
    ): StreamedResponse
    {
        $this->authorize('view', $myVacancy);

        $path = $vacancyApplication->cv_path;

        if ($path === null || !$cvStorage->adapter()->has($path)) {
            throw new NotFoundHttpException('CV file not found for application ' . $vacancyApplication->id);
        }

        $ext = pathinfo($path)['extension'] ?? 'unknown';

        return $cvStorage->adapter()->download(
            $path,
            'CV letter for ' . $myVacancy->title . ' from ' . $vacancyApplication->user->name . '.' . $ext
        );
    }
}
