<?php

namespace App\Http\Controllers;

use App\Http\Requests\VacancyStoreRequest;
use App\Models\Employer;
use App\Models\Vacancy;
use Illuminate\Http\Request;

class MyVacancyController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Vacancy::class, 'vacancy');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('my-vacancy.index');
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('my-vacancy.create');
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(VacancyStoreRequest $request)
    {
        /** @var Employer $employer */
        $employer = $request->user()->employer;
        $vacancy = Vacancy::make($request->validated());
        $employer->vacancies()->save($vacancy);

        return back()->with('success', 'Vacancy was created');
    }


    /**
     * Display the specified resource.
     */
    public function show(Vacancy $vacancy)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Vacancy $vacancy)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Vacancy $vacancy)
    {
        //
    }
}
