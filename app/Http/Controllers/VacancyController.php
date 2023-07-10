<?php

namespace App\Http\Controllers;

use App\Dto\FilterVacancyDto;
use App\Http\Requests\VacanciesIndexRequest;
use App\Models\Vacancy;
use Illuminate\Http\Request;

class VacancyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(VacanciesIndexRequest $request)
    {
        $dto = new FilterVacancyDto(...$request->validated());
        $vacancies = Vacancy::filter($dto);

        return view('vacancies.index', ['vacancies' => $vacancies->get()]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Vacancy $vacancy)
    {
        return view('vacancies.show', compact('vacancy'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Vacancy $job)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Vacancy $job)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Vacancy $job)
    {
        //
    }
}