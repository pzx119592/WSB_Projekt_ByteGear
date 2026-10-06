@extends('layouts.app')
@section('title','Produkty')
@section('content')
<section class="hero">
<div>
<span class="eyebrow">TWOJE STANOWISKO. TWOJE ZASADY.</span>
<h1>Małe zmiany.<br>Większy komfort.</h1>
<p>Klawiatury, myszy i akcesoria, z którymi praca i granie stają się przyjemniejsze.</p>
<a class="button" href="#produkty">Przeglądaj produkty →</a>
</div>
<img src="{{ asset('images/keyboard.svg') }}" alt="Ilustracja klawiatury komputerowej">
</section>
<div class="category-links">
<a href="{{ route('catalog') }}">Wszystko</a>@foreach($categories as $category)<a @class(['selected' => (string)request('category') === (string)$category->id]) href="{{ route('catalog',['category'=>$category->id]) }}">{{ $category->name }}</a>@endforeach</div>
<div class="section-heading" id="produkty">
<div>
<span class="eyebrow">ZNAJDŹ SWÓJ SPRZĘT</span>
<h2>Produkty</h2>
</div>
<span class="muted">{{ $products->total() }} produktów</span>
</div>
<form class="filters" method="GET" action="{{ route('catalog') }}">
<label>Szukaj<input name="q" value="{{ request('q') }}" placeholder="Nazwa produktu">
</label>
<label>Kategoria<select name="category">
<option value="">Wszystkie</option>@foreach($categories as $category)<option value="{{ $category->id }}" @selected(request('category') == $category->id)>{{ $category->name }}</option>@endforeach</select>
</label>
<label>Cena od (zł)<input name="min" type="number" min="0" step="0.01" value="{{ request('min') }}">
</label>
<label>Cena do (zł)<input name="max" type="number" min="0" step="0.01" value="{{ request('max') }}">
</label>
<label>Sortowanie<select name="sort">@foreach(['new'=>'Najnowsze','price_asc'=>'Cena rosnąco','price_desc'=>'Cena malejąco','name'=>'Nazwa A–Z'] as $key=>$label)<option value="{{ $key }}" @selected(request('sort','new') === $key)>{{ $label }}</option>@endforeach</select>
</label>
<label class="check">
<input type="checkbox" name="available" value="1" @checked(request('available'))> Dostępne</label>
<button>Filtruj</button>
<a href="{{ route('catalog') }}">Wyczyść</a>
</form>
<div class="product-grid">@forelse($products as $product)<article class="product-card">
<a class="product-image" href="{{ route('products.show',$product) }}">
<img src="{{ asset('images/'.$product->category->slug.'.svg') }}" alt="{{ $product->category->name }}">
</a>
<div class="card-body">
<span class="muted">{{ $product->category->name }}</span>
<h3>
<a href="{{ route('products.show',$product) }}">{{ $product->name }}</a>
</h3>
<p class="availability">{{ $product->stock > 0 ? '● Dostępny' : 'Chwilowo niedostępny' }}</p>
<div class="card-bottom">
<strong class="price">{{ number_format($product->price_grosze/100,2,',',' ') }} zł</strong>
<form action="{{ route('cart.add',$product) }}" method="POST">@csrf<input type="hidden" name="quantity" value="1">
<button class="small" @disabled($product->stock === 0) aria-label="Dodaj {{ $product->name }} do koszyka">+ Koszyk</button>
</form>
</div>
</div>
</article>@empty<p>Brak produktów pasujących do filtrów.</p>@endforelse</div>
<div class="pagination">{{ $products->links('pagination::simple-default') }}</div>
@endsection
