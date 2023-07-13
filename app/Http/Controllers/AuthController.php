<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function create()
    {
        return view('auth.create');
    }

    public function store(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

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
