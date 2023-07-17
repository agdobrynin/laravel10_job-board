<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthStoreRequest;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function __construct()
    {
        // TODO make config for this
        $this->middleware('throttle:6,1')
            ->only(['store']);
    }

    public function create()
    {
        return view('auth.create');
    }

    public function store(AuthStoreRequest $request)
    {
        $credentials = $request->validated();

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            return redirect()->intended();
        }

        return redirect()->back()
            ->with('error', 'Invalid credentials');
    }

    public function destroy()
    {
        Auth::logout();

        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect('/');
    }
}
