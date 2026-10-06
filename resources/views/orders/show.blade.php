@extends('layouts.app')
@section('title','Szczegóły zamówienia')
@section('content')<h1>Zamówienie #{{ $order->id }}</h1>
<p>{{ $order->created_at->format('d.m.Y H:i') }} · <strong>{{ \App\Models\Order::STATUSES[$order->status] }}</strong>
</p>
<div class="checkout-grid">
<div class="panel">
<h2>Produkty</h2>@foreach($order->items as $item)<p>{{ $item->name }} × {{ $item->quantity }} <strong>{{ number_format($item->price_grosze*$item->quantity/100,2,',',' ') }} zł</strong>
</p>@endforeach<hr>
<p class="price">Razem {{ number_format($order->total_grosze/100,2,',',' ') }} zł</p>
</div>
<div class="panel">
<h2>Dane dostawy</h2>
<p>{{ $order->name }}<br>{{ $order->address }}<br>{{ $order->postcode }} {{ $order->city }}</p>
<p>{{ $order->email }}</p>@if(in_array(auth()->user()->role,['admin','moderator']) && $order->status !== 'completed')<form method="POST" action="{{ route('admin.orders.update',$order) }}">@csrf @method('PATCH')<input type="hidden" name="status" value="{{ $order->status === 'new' ? 'processing' : 'completed' }}">
<button>{{ $order->status === 'new' ? 'Rozpocznij realizację' : 'Oznacz jako zrealizowane' }}</button>
</form>@endif</div>
</div>
<p>
<a href="{{ route('dashboard') }}">← Wróć do panelu</a>
</p>@endsection
