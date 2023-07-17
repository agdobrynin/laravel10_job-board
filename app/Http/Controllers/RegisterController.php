<?php

namespace App\Http\Controllers;

use App\Dto\RegisterUserDto;
use App\Http\Requests\RegisterStoreRequest;
use App\Models\Employer;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(RegisterStoreRequest $request)
    {
        $dto = new RegisterUserDto(...$request->validated());

        /** @var User $user */
        $user = User::create([
            'name' => $dto->name,
            'email' => $dto->email,
            'password' => Hash::make($dto->password),
        ]);

        if ($dto->is_employer && $dto->employer_name) {
            $user->employer()
                ->save(
                    Employer::make(['name' => $dto->employer_name])
                );
        }

        Auth::login($user);

        event(new Registered($user));

        return redirect()
            ->route('vacancies.index')
            ->with('success', 'Registration success. We mailed confirmation link your email.');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('reg.create');
    }
}
