@extends('layouts.app')
@section('title','Cena w euro')
@section('content')<h1>Orientacyjna cena w EUR</h1>
<h2>{{ $product->name }}</h2>@isset($error)<p>{{ $error }}</p>@else<p class="price large">{{ number_format($euro,2,',',' ') }} EUR</p>
<p>Kurs średni NBP: {{ $rate['rates'][0]['mid'] }} PLN. Data: {{ $rate['rates'][0]['effectiveDate'] }}.</p>
<p>Zakupy rozliczamy w PLN. Wartość EUR służy do porównania.</p>@endisset<a href="{{ route('products.show',$product) }}">← Wróć do produktu</a>@endsection
