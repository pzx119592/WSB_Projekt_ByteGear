<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\AccountMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function loginForm()
    {
        return view('auth.login');
    }

    public function registerForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|min:3|max:100', 'email' => 'required|email|max:255|unique:users',
            'password' => ['required', 'string', 'min:8', 'max:72', 'confirmed', 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9]).+$/s'],
        ]);
        $data['role'] = 'customer';
        $user = User::create($data);
        $sent = app(AccountMail::class)->verify($user);

        return redirect()->route('login')->with('status', $sent
            ? 'Konto utworzone. Zaloguj się i potwierdź adres e-mail, korzystając z wysłanej wiadomości.'
            : 'Konto utworzone. Zaloguj się, aby ponowić wysyłanie wiadomości aktywacyjnej.');
    }

    public function login(Request $request)
    {
        $data = $request->validate(['email' => 'required|email', 'password' => 'required|string']);
        if (! Auth::attempt($data)) {
            return back()->withErrors(['email' => 'Nieprawidłowy e-mail lub hasło.'])->onlyInput('email');
        }
        $request->session()->regenerate();

        // Każda rola otrzymuje swój ekran, bez przekierowania do panelu innej roli.
        return redirect()->route('dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('catalog')->with('status', 'Wylogowano.');
    }
}
