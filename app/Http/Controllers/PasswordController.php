<?php

namespace App\Http\Controllers;

use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class PasswordController extends Controller
{
    public function request()
    {
        return view('auth.forgot-password');
    }

    public function email(Request $request)
    {
        $data = $request->validate(['email' => 'required|email|max:255']);
        try {
            Password::sendResetLink($data);
        } catch (TransportExceptionInterface $exception) {
            report($exception);
        }

        // Taka sama odpowiedź dla istniejącego i nieznanego adresu chroni listę kont.
        return back()->with('status', 'Jeśli konto z tym adresem istnieje, sprawdź pocztę w poszukiwaniu linku do zmiany hasła. Jeśli wiadomość nie dotrze, spróbuj ponownie później.');
    }

    public function form(Request $request, string $token)
    {
        return view('auth.reset-password', ['token' => $token, 'email' => $request->query('email', '')]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'token' => 'required|string', 'email' => 'required|email|max:255',
            'password' => ['required', 'string', 'min:8', 'max:72', 'confirmed', 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9]).+$/s'],
        ]);
        $status = Password::reset($data, function ($user, $password) {
            $user->forceFill(['password' => Hash::make($password), 'remember_token' => Str::random(60)])->save();
            if (config('session.driver') === 'database') {
                DB::table(config('session.table', 'sessions'))->where('user_id', $user->id)->delete();
            }
            event(new PasswordReset($user));
        });
        if ($status !== Password::PasswordReset) {
            return back()->withErrors(['email' => 'Link jest nieprawidłowy lub wygasł. Poproś o nowy link do zmiany hasła.'])->onlyInput('email');
        }

        return redirect()->route('login')->with('status', 'Hasło zostało zmienione. Zaloguj się nowym hasłem.');
    }
}
