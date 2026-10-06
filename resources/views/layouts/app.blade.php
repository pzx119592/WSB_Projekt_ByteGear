<!DOCTYPE html>
<html lang="pl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>@yield('title', 'Sklep komputerowy') · {{ config('app.name') }}</title>
<link rel="stylesheet" href="{{ asset('css/shop.css') }}">
</head>
<body>
<div class="topline">Peryferia do pracy i grania <span>Projekt zaliczeniowy · sklep demonstracyjny</span>
</div>
<header class="header">
<a class="brand" href="{{ route('catalog') }}">
<span class="brandmark" aria-hidden="true">▦</span>{{ config('app.name') }}</a>
<nav aria-label="Menu główne">
<a href="{{ route('catalog') }}">Produkty</a>
<a href="{{ route('cart') }}">Koszyk <span class="count">{{ array_sum(session('cart', [])) }}</span>
</a>
@auth <a href="{{ route('dashboard') }}">Moje konto</a>
<form method="POST" action="{{ route('logout') }}">@csrf<button class="text-button">Wyloguj</button>
</form>
@else <a href="{{ route('login') }}">Zaloguj się</a>
<a class="button small" href="{{ route('register') }}">Załóż konto</a> @endauth</nav>
</header>
<main class="wrap">
@auth @if(in_array(auth()->user()->role, ['admin','moderator']))
<nav class="staff-nav" aria-label="Panel zarządzania">
<strong>{{ auth()->user()->role === 'admin' ? 'Administrator' : 'Moderator' }}</strong>
<a href="{{ route('admin.products.index') }}">Produkty</a>
<a href="{{ route('admin.orders.index') }}">Zamówienia</a>@if(auth()->user()->role === 'admin')<a href="{{ route('admin.users.index') }}">Użytkownicy</a>@endif</nav>
@endif @endauth
@if(session('status'))<p class="notice" role="status">{{ session('status') }}</p>@endif
@if($errors->any())<div class="errors" role="alert">
<strong>Sprawdź formularz:</strong>
<ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
</div>@endif
@yield('content')
</main>
<footer>
<strong>{{ config('app.name') }}</strong>
<span>Sprzęt, który pasuje do Twojego stanowiska.</span>
<small>Ceny i produkty są danymi demonstracyjnymi. Zamówienia nie powodują rzeczywistych zakupów.</small>
</footer>
</body>
</html>
