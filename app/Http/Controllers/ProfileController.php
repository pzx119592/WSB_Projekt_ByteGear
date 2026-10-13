<?php

namespace App\Http\Controllers;

use App\Services\AccountMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function edit(Request $request)
    {
        return view('auth.profile', ['user' => $request->user()]);
    }

    public function update(Request $request, AccountMail $mail)
    {
        $user = $request->user();
        $emailChanged = $request->input('email') !== $user->email;
        $data = $request->validate([
            'name' => 'required|string|min:3|max:100',
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'current_password' => [$emailChanged ? 'required' : 'nullable', 'current_password:web'],
        ]);
        $user->fill(['name' => $data['name'], 'email' => $data['email']]);
        if ($emailChanged) {
            $user->email_verified_at = null;
        }
        $user->save();
        if ($emailChanged) {
            $sent = $mail->verify($user);

            return redirect()->route('verification.notice')->with('status', $sent
                ? 'Dane zapisane. Potwierdź nowy adres e-mail w wysłanej wiadomości.'
                : 'Dane zapisane. Ponów wysyłkę wiadomości potwierdzającej nowy adres.');
        }

        return back()->with('status', 'Dane profilu zostały zapisane.');
    }

    public function password(Request $request)
    {
        $data = $request->validate([
            'current_password' => 'required|current_password:web',
            'password' => ['required', 'string', 'min:8', 'max:72', 'confirmed', 'different:current_password', 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9]).+$/s'],
        ]);
        $user = $request->user();
        $user->forceFill(['password' => $data['password'], 'remember_token' => Str::random(60)])->save();
        $request->session()->regenerate();
        $request->session()->put('password_hash_web', $user->password);
        if (config('session.driver') === 'database') {
            DB::table(config('session.table', 'sessions'))->where('user_id', $user->id)
                ->where('id', '!=', $request->session()->getId())->delete();
        }

        return back()->with('status', 'Hasło zmienione. Pozostałe sesje zostały wylogowane.');
    }
}
