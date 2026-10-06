@extends('layouts.app')
@section('title','Użytkownicy')
@section('content')<div class="section-heading">
<h1>Użytkownicy</h1>
<a class="button" href="{{ route('admin.users.create') }}">+ Dodaj użytkownika</a>
</div>
<div class="table-wrap">
<table>
<thead>
<tr>
<th>Imię i nazwisko</th>
<th>E-mail</th>
<th>Rola</th>
<th>Akcje</th>
</tr>
</thead>
<tbody>@foreach($users as $user)<tr>
<td>{{ $user->name }}</td>
<td>{{ $user->email }}</td>
<td>{{ ['admin'=>'Administrator','moderator'=>'Moderator','customer'=>'Klient'][$user->role] }}</td>
<td>
<a href="{{ route('admin.users.show',$user) }}">Szczegóły</a> · <a href="{{ route('admin.users.edit',$user) }}">Edytuj</a>
</td>
</tr>@endforeach</tbody>
</table>
</div>{{ $users->links('pagination::simple-default') }}@endsection
