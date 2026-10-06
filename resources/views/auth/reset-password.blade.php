@extends('layouts.app')
@section('title', 'Nowe hasło')
@section('content')
<section class="form-box">
<span class="eyebrow">ODZYSKAJ DOSTĘP</span>
<h1>Ustaw nowe hasło</h1>
<p class="muted">Minimum 8 znaków, mała i wielka litera oraz cyfra.</p>
<form method="POST" action="{{ route('password.update') }}">@csrf
<input type="hidden" name="token" value="{{ $token }}">
<label>E-mail<input type="email" name="email" value="{{ old('email', $email) }}" autocomplete="email" required></label>
<label>Nowe hasło<input type="password" name="password" autocomplete="new-password" minlength="8" maxlength="72" required></label>
<label>Powtórz hasło<input type="password" name="password_confirmation" autocomplete="new-password" minlength="8" maxlength="72" required></label>
<button>Zapisz nowe hasło</button>
</form>
</section>
@endsection
