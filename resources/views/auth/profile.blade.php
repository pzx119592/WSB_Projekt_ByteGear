@extends('layouts.app')

@section('title', 'Mój profil')

@section('content')

    <div class="page-heading">
        <div><span class="eyebrow">USTAWIENIA KONTA</span>
            <h1>Mój profil</h1>
            <p class="muted">Zaktualizuj dane kontaktowe i zadbaj o bezpieczeństwo konta.</p>
        </div><a href="{{ route('dashboard') }}">← Wróć do panelu</a>
    </div>
    <div class="profile-grid">
        <section class="panel">
            <h2>Dane osobowe</h2>
            <form method="POST" action="{{ route('profile.update') }}">
                @csrf

                @method('PATCH')

                <label>
                    Imię i nazwisko
                    <input name="name" value="{{ old('name', $user->name) }}" autocomplete="name"
                        minlength="3" maxlength="100" required></label>
                <label>
                    E-mail
                    <input type="email" name="email" value="{{ old('email', $user->email) }}"
                        autocomplete="email" required></label>
                <p class="muted">Zmiana e-maila wymaga obecnego hasła i potwierdzenia nowego adresu. Do
                    czasu aktywacji panel zakupów będzie zablokowany.</p>
                <label>
                    Obecne hasło — przy zmianie e-maila
                    <input type="password" name="current_password"
                        autocomplete="current-password"></label>
                <button>Zapisz dane</button>
            </form>
        </section>
        <section class="panel">
            <h2>Zmiana hasła</h2>
            <p class="muted">Minimum 8 znaków, mała i wielka litera oraz cyfra. Pozostałe sesje zostaną
                wylogowane.</p>
            <form method="POST" action="{{ route('profile.password') }}">
                @csrf

                @method('PUT')

                <label>
                    Obecne hasło
                    <input type="password" name="current_password" autocomplete="current-password"
                        required></label>
                <label>
                    Nowe hasło
                    <input type="password" name="password" autocomplete="new-password" minlength="8"
                        maxlength="72" required></label>
                <label>
                    Powtórz nowe hasło
                    <input type="password" name="password_confirmation" autocomplete="new-password"
                        required></label>
                <button>Zmień hasło</button>
            </form>
        </section>
    </div>

@endsection
