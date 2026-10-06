@extends('layouts.app')
@section('title','Zamówienia')
@section('content')<h1>{{ isset($staff) ? 'Zamówienia klientów' : 'Moje zamówienia' }}</h1>
<div class="table-wrap">
<table>
<thead>
<tr>
<th>Numer</th>
<th>Data</th>
<th>Klient</th>
<th>Kwota</th>
<th>Status</th>
<th>
</th>
</tr>
</thead>
<tbody>@forelse($orders as $order)<tr>
<td>#{{ $order->id }}</td>
<td>{{ $order->created_at->format('d.m.Y H:i') }}</td>
<td>{{ $order->name }}</td>
<td>{{ number_format($order->total_grosze/100,2,',',' ') }} zł</td>
<td>{{ \App\Models\Order::STATUSES[$order->status] }}</td>
<td>
<a href="{{ route('orders.show',$order) }}">Szczegóły</a>
</td>
</tr>@empty<tr>
<td colspan="6">Brak zamówień.</td>
</tr>@endforelse</tbody>
</table>
</div>{{ $orders->links('pagination::simple-default') }}@endsection
