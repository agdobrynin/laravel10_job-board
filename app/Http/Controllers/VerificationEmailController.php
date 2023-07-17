<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

class VerificationEmailController extends Controller
{
    public function notice(Request $request)
    {
        return $request->user()->hasVerifiedEmail()
            ? to_route('vacancies.index')
            : view('auth.verify-email');
    }

    public function verification(EmailVerificationRequest $request)
    {
        $request->fulfill();

        return to_route('vacancies.index');
    }

    public function resending(Request $request)
    {
        $request->user()->sendEmailVerificationNotification();

        return to_route('vacancies.index')
            ->with('success', 'Verification link sent! Please check your mailbox and confirm email.');
    }
}
