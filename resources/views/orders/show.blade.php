@extends('layouts.app')
@section('title','Szczegóły zamówienia')
@section('content')<h1>Zamówienie #{{ $order->id }}</h1>
<p>{{ $order->created_at->format('d.m.Y H:i') }} · <span class="status status-{{ $order->status }}">{{ \App\Models\Order::STATUSES[$order->status] }}</span>
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
<p>{{ $order->email }}</p>@if(in_array(auth()->user()->role,['admin','moderator']) && in_array($order->status,['new','processing']))<form method="POST" action="{{ route('admin.orders.update',$order) }}">@csrf @method('PATCH')<input type="hidden" name="status" value="{{ $order->status === 'new' ? 'processing' : 'completed' }}">
<button>{{ $order->status === 'new' ? 'Rozpocznij realizację' : 'Oznacz jako zrealizowane' }}</button>
</form>@endif</div>
</div>
@if($order->cancelled_at)<p class="notice">Anulowano {{ $order->cancelled_at->format('d.m.Y H:i') }}. Zamówienie pozostaje w historii.</p>@endif
@if($order->user_id === auth()->id() && $order->status === 'new')
<section class="panel cancel-panel"><h2>Anulowanie zamówienia</h2><p>Możesz zrezygnować, dopóki realizacja się nie rozpoczęła. Produkty wrócą do magazynu.</p>
<form method="POST" action="{{ route('orders.cancel', $order) }}">@csrf @method('PATCH')<label class="check"><input type="checkbox" name="confirm_cancel" value="1" required>Potwierdzam anulowanie tego zamówienia</label><button class="danger">Anuluj zamówienie</button></form></section>
@endif
@if($order->user_id === auth()->id() && ! $order->confirmation_sent_at && $order->status !== 'cancelled')
<section class="panel"><p>Potwierdzenie e-mailem nie zostało jeszcze wysłane.</p><form method="POST" action="{{ route('orders.confirmation', $order) }}">@csrf<button>Wyślij potwierdzenie</button></form></section>
@endif
<p>
<a href="{{ route('dashboard') }}">← Wróć do panelu</a>
</p>@endsection
