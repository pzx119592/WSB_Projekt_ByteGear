<?php

namespace App\Http\Controllers;

use App\Services\AccountMail;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

class VerificationController extends Controller
{
    public function notice(Request $request)
    {
        return $request->user()->hasVerifiedEmail()
            ? redirect()->route('dashboard')
            : view('auth.verify-email');
    }

    public function verify(EmailVerificationRequest $request)
    {
        $request->fulfill();

        return redirect()->route('dashboard')->with('status', 'Adres e-mail został potwierdzony. Konto jest aktywne.');
    }

    public function send(Request $request, AccountMail $mail)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->route('dashboard');
        }
        if (! $mail->verify($request->user())) {
            return back()->withErrors(['email' => 'Nie udało się wysłać wiadomości. Spróbuj ponownie później.']);
        }

        return back()->with('status', 'Wysłano nowy link aktywacyjny. Sprawdź pocztę.');
    }
}
