@extends('layouts.app')
@section('title', 'Zapomniane hasło')
@section('content')
<section class="form-box">
<span class="eyebrow">ODZYSKAJ DOSTĘP</span>
<h1>Nie pamiętasz hasła?</h1>
<p class="muted">Podaj adres e-mail swojego konta. Otrzymasz link do ustawienia nowego hasła.</p>
<form method="POST" action="{{ route('password.email') }}">@csrf
<label>E-mail<input type="email" name="email" value="{{ old('email') }}" autocomplete="email" required></label>
<button>Wyślij link</button>
</form>
<p><a href="{{ route('login') }}">Wróć do logowania</a></p>
</section>
@endsection
