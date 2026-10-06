@extends('layouts.app')
@section('title','Rejestracja')
@section('content')<section class="form-box">
<h1>Utwórz konto</h1>
<form method="POST" action="{{ route('register.store') }}">@csrf<label>Imię i nazwisko<input name="name" value="{{ old('name') }}" autocomplete="name" minlength="3" required>
</label>
<label>E-mail<input type="email" name="email" value="{{ old('email') }}" autocomplete="email" required>
</label>
<label>Hasło<input type="password" name="password" autocomplete="new-password" minlength="8" maxlength="72" required>
</label>
<small>Minimum 8 znaków: mała i duża litera oraz cyfra.</small>
<label>Powtórz hasło<input type="password" name="password_confirmation" autocomplete="new-password" required>
</label>
<button>Utwórz konto</button>
</form>
<p>Masz konto? <a href="{{ route('login') }}">Zaloguj się</a>
</p>
</section>@endsection
