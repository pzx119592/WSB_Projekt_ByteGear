@extends('layouts.app')
@section('title','Zarządzanie produktami')
@section('content')<div class="section-heading">
<h1>Produkty sklepu</h1>
<a class="button" href="{{ route('admin.products.create') }}">+ Dodaj produkt</a>
</div>
<div class="table-wrap">
<table>
<thead>
<tr>
<th>Produkt</th>
<th>SKU</th>
<th>Cena</th>
<th>Stan</th>
<th>Sprzedaż</th>
<th>Akcje</th>
</tr>
</thead>
<tbody>@foreach($products as $product)<tr>
<td>{{ $product->name }}<small>{{ $product->category->name }}</small>
</td>
<td>{{ $product->sku }}</td>
<td>{{ number_format($product->price_grosze/100,2,',',' ') }} zł</td>
<td>{{ $product->stock }}</td>
<td>{{ $product->active ? 'Aktywny' : 'Wycofany' }}</td>
<td>
<a href="{{ route('admin.products.edit',$product) }}">Edytuj</a>@if($product->active)<form method="POST" action="{{ route('admin.products.destroy',$product) }}">@csrf @method('DELETE')<button class="text-button red">Wycofaj</button>
</form>@endif</td>
</tr>@endforeach</tbody>
</table>
</div>{{ $products->links('pagination::simple-default') }}@endsection
