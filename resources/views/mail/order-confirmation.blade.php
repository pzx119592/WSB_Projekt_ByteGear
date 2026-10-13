<!DOCTYPE html><html lang="pl"><head><meta charset="UTF-8"><title>Potwierdzenie zamówienia</title></head>
<body style="margin:0;background:#f3f6fb;font-family:Arial,sans-serif;color:#1b2638;padding:24px">
<div style="max-width:620px;margin:auto;background:white;padding:28px;border-radius:12px">
<p style="font-size:22px;font-weight:bold;color:#2259dd">{{ config('app.name') }}</p>
<h1 style="font-size:24px">Dziękujemy za zamówienie #{{ $order->id }}</h1>
<p>Cześć, {{ $order->name }}! Poniżej znajdziesz podsumowanie zakupu z {{ $order->created_at->format('d.m.Y H:i') }}.</p>
<table style="width:100%;border-collapse:collapse"><caption style="text-align:left;font-weight:bold;padding:10px 0">Produkty</caption><thead><tr><th style="text-align:left;padding:10px;border-bottom:1px solid #dce3ef">Produkt</th><th style="text-align:center;padding:10px;border-bottom:1px solid #dce3ef">Szt.</th><th style="text-align:right;padding:10px;border-bottom:1px solid #dce3ef">Kwota</th></tr></thead><tbody>
@foreach($order->items as $item)<tr><td style="padding:10px;border-bottom:1px solid #dce3ef">{{ $item->name }}</td><td style="text-align:center;padding:10px;border-bottom:1px solid #dce3ef">{{ $item->quantity }}</td><td style="text-align:right;padding:10px;border-bottom:1px solid #dce3ef">{{ number_format($item->price_grosze*$item->quantity/100,2,',',' ') }} zł</td></tr>@endforeach
</tbody></table>
<p style="font-size:20px;text-align:right;font-weight:bold">Razem: {{ number_format($order->total_grosze/100,2,',',' ') }} zł</p>
<h2 style="font-size:18px">Dostawa</h2><p>{{ $order->address }}<br>{{ $order->postcode }} {{ $order->city }}</p>
<p><a href="{{ route('orders.show', $order) }}" style="display:inline-block;background:#2259dd;color:white;padding:12px 18px;text-decoration:none;border-radius:8px">Zobacz zamówienie</a></p>
<p style="font-size:12px;color:#536176">To sklep demonstracyjny. Zamówienie nie powoduje rzeczywistego zakupu ani płatności. Link otwórz na komputerze, na którym uruchomiono sklep.</p>
</div></body></html>
