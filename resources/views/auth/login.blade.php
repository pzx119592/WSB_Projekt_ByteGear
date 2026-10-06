@extends('layouts.app')
@section('title','Logowanie')
@section('content')<section class="form-box">
<span class="eyebrow">DOBRZE CIĘ WIDZIEĆ</span>
<h1>Zaloguj się</h1>
<p class="muted">Sprawdź zamówienia lub dokończ zakupy.</p>
<form method="POST" action="{{ route('login.store') }}">@csrf<label>E-mail<input type="email" name="email" autocomplete="email" value="{{ old('email') }}" required>
</label>
<label>Hasło<input type="password" name="password" autocomplete="current-password" required>
</label>
<button>Zaloguj się</button>
</form>
<p>Nie masz konta? <a href="{{ route('register') }}">Zarejestruj się</a>
</p>
</section>@endsection
