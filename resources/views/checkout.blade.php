@extends('layouts.app')
@section('title','Zamówienie')
@section('content')<h1>Dokończ zamówienie</h1>
<div class="checkout-grid">
<form class="panel" method="POST" action="{{ route('checkout.store') }}">@csrf<input type="hidden" name="checkout_token" value="{{ session('checkout_token') }}">
<h2>Dane dostawy</h2>
<label>Imię i nazwisko<input name="name" value="{{ old('name',auth()->user()->name) }}" autocomplete="name" required>
</label>
<label>Ulica i numer<input name="address" value="{{ old('address') }}" autocomplete="street-address" required>
</label>
<label>Kod pocztowy<input name="postcode" value="{{ old('postcode') }}" placeholder="00-000" pattern="[0-9]{2}-[0-9]{3}" autocomplete="postal-code" required>
</label>
<label>Miasto<input name="city" value="{{ old('city') }}" autocomplete="address-level2" required>
</label>
<p>Potwierdzenie w Twoim koncie: {{ auth()->user()->email }}</p>
<p class="muted">Dostawa demonstracyjna: 0,00 zł. Płatność demonstracyjna — bez pobierania pieniędzy.</p>
<button>Złóż zamówienie</button>
</form>
<aside class="panel">
<h2>Podsumowanie</h2>@foreach($rows as $row)<p>{{ $row['product']->name }} × {{ $row['quantity'] }} <strong>{{ number_format($row['subtotal']/100,2,',',' ') }} zł</strong>
</p>@endforeach<hr>
<p class="price">Razem {{ number_format($total/100,2,',',' ') }} zł</p>
<a href="{{ route('cart') }}">Zmień koszyk</a>
</aside>
</div>@endsection
