@extends('layouts.app')
@section('title','Użytkownik')
@section('content')<div class="panel">
<h1>{{ $user->name }}</h1>
<p>{{ $user->email }}</p>
<p>Rola: {{ ['admin'=>'Administrator','moderator'=>'Moderator','customer'=>'Klient'][$user->role] }}</p>
<p>Dołączył: {{ $user->created_at->format('d.m.Y H:i') }}</p>
<a class="button" href="{{ route('admin.users.edit',$user) }}">Edytuj użytkownika</a>@if($user->id !== auth()->id())<form class="delete-box" method="POST" action="{{ route('admin.users.destroy',$user) }}">@csrf @method('DELETE')<p>Usunięcie konta odbierze dostęp. Historia jego zamówień zostanie zachowana.</p>
<button class="danger">Usuń użytkownika</button>
</form>@endif</div>
<p>
<a href="{{ route('admin.users.index') }}">← Lista użytkowników</a>
</p>@endsection
