@extends('layouts.app')
@section('title','Koszyk')
@section('content')<h1>Twój koszyk</h1>@if(!$rows)<div class="empty">
<h2>Tu znajdzie się Twój nowy sprzęt.</h2>
<p>Dodaj coś z katalogu i wróć do koszyka.</p>
<a class="button" href="{{ route('catalog') }}">Zobacz produkty</a>
</div>@else<div class="table-wrap">
<table>
<thead>
<tr>
<th>Produkt</th>
<th>Cena</th>
<th>Ilość</th>
<th>Suma</th>
<th>
</th>
</tr>
</thead>
<tbody>@foreach($rows as $row)<tr>
<td>
<a href="{{ route('products.show',$row['product']) }}">{{ $row['product']->name }}</a>@if(!$row['product']->active || $row['quantity'] > $row['product']->stock)<p class="red">Produkt lub liczba sztuk niedostępne</p>@endif</td>
<td>{{ number_format($row['product']->price_grosze/100,2,',',' ') }} zł</td>
<td>
<form class="inline" method="POST" action="{{ route('cart.update',$row['product']) }}">@csrf @method('PATCH')<input aria-label="Liczba sztuk {{ $row['product']->name }}" type="number" name="quantity" value="{{ $row['quantity'] }}" min="1" max="99">
<button class="small secondary">Zmień</button>
</form>
</td>
<td>{{ number_format($row['subtotal']/100,2,',',' ') }} zł</td>
<td>
<form method="POST" action="{{ route('cart.destroy',$row['product']) }}">@csrf @method('DELETE')<button class="text-button red">Usuń</button>
</form>
</td>
</tr>@endforeach</tbody>
</table>
</div>
<div class="summary">
<h2>Razem: {{ number_format($total/100,2,',',' ') }} zł</h2>
<a class="button" href="{{ route('checkout') }}">Przejdź do zamówienia →</a>
</div>@endif @endsection
