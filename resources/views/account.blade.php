@extends('layouts.app')
@section('title','Moje konto')
@section('content')<span class="eyebrow">PANEL KLIENTA</span>
<h1>Cześć, {{ auth()->user()->name }}!</h1>
<p>{{ auth()->user()->email }}</p>
<a class="button" href="{{ route('catalog') }}">Przejdź do sklepu</a>
<h2>Ostatnie zamówienia</h2>@forelse($orders as $order)<p>
<a href="{{ route('orders.show',$order) }}">Zamówienie #{{ $order->id }}</a> · {{ $order->created_at->format('d.m.Y') }} · {{ \App\Models\Order::STATUSES[$order->status] }}</p>@empty<p>Nie masz jeszcze zamówień.</p>@endforelse<a href="{{ route('orders.index') }}">Wszystkie moje zamówienia →</a>@endsection
