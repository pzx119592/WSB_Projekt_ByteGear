@extends('layouts.app')
@section('title','Produkt')
@section('content')<div class="form-box wide">
<h1>{{ $product->exists ? 'Edytuj produkt' : 'Dodaj produkt' }}</h1>
<form method="POST" action="{{ $product->exists ? route('admin.products.update',$product) : route('admin.products.store') }}">@csrf @if($product->exists) @method('PUT') @endif<label>Nazwa<input name="name" value="{{ old('name',$product->name) }}" required>
</label>
<label>Kategoria<select name="category_id">@foreach($categories as $category)<option value="{{ $category->id }}" @selected(old('category_id',$product->category_id) == $category->id)>{{ $category->name }}</option>@endforeach</select>
</label>
<label>SKU<input name="sku" value="{{ old('sku',$product->sku) }}" pattern="[A-Z0-9-]+" required>
</label>
<small>Wielkie litery, cyfry i myślnik, np. KEY-001.</small>
<label>Opis<textarea name="description" rows="5" required>{{ old('description',$product->description) }}</textarea>
</label>
<label>Cena w zł<input type="number" name="price" step="0.01" min="0.01" value="{{ old('price',$product->exists ? $product->price_grosze/100 : '') }}" required>
</label>
<label>Stan magazynowy<input type="number" name="stock" min="0" value="{{ old('stock',$product->stock) }}" required>
</label>
<input type="hidden" name="active" value="0">
<label class="check">
<input type="checkbox" name="active" value="1" @checked(old('active',$product->active))> Aktywny w sklepie</label>
<button>Zapisz produkt</button>
<a href="{{ route('admin.products.index') }}">Anuluj</a>
</form>
</div>@endsection
