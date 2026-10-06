@extends('layouts.app')
@section('title', 'Aktywacja konta')
@section('content')
<section class="form-box">
<span class="eyebrow">JESZCZE JEDEN KROK</span>
<h1>Potwierdź adres e-mail</h1>
<p>Wiadomość aktywacyjna została wysłana na <strong>{{ auth()->user()->email }}</strong>. Kliknij w niej „Aktywuj konto”. Link jest ważny przez 60 minut.</p>
<p class="muted">Potwierdzenie adresu umożliwi korzystanie z panelu i składanie zamówień. Sprawdź też folder spam.</p>
<form method="POST" action="{{ route('verification.send') }}">@csrf
<button>Wyślij link ponownie</button>
</form>
<p><a href="{{ route('catalog') }}">Wróć do produktów</a></p>
</section>
@endsection
