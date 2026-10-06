@extends('layouts.app')
@section('title',$product->name)
@section('content')<a href="{{ route('catalog') }}">← Wróć do produktów</a>
<section class="product-detail">
<div class="detail-image">
<img src="{{ asset('images/'.$product->category->slug.'.svg') }}" alt="{{ $product->category->name }}">
</div>
<div>
<span class="eyebrow">{{ $product->category->name }}</span>
<h1>{{ $product->name }}</h1>
<p class="muted">Kod produktu: {{ $product->sku }}</p>
<p class="description">{{ $product->description }}</p>
<p class="price large">{{ number_format($product->price_grosze/100,2,',',' ') }} zł</p>
<p class="availability">{{ $product->stock > 0 ? 'Dostępny: '.$product->stock.' szt.' : 'Chwilowo niedostępny' }}</p>
<form class="inline" method="POST" action="{{ route('cart.add',$product) }}">@csrf<label>Ilość<input type="number" name="quantity" min="1" max="{{ min(99,$product->stock) }}" value="1" required>
</label>
<button @disabled($product->stock === 0)>Dodaj do koszyka</button>
</form>
<p>
<a href="{{ route('products.euro',$product) }}">Sprawdź orientacyjną cenę w EUR (NBP)</a>
</p>
</div>
</section>@endsection
