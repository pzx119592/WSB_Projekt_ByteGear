@extends('layouts.app')
@section('title','Użytkownik')
@section('content')<div class="form-box">
<h1>{{ $user->exists ? 'Edytuj użytkownika' : 'Dodaj użytkownika' }}</h1>
<form method="POST" action="{{ $user->exists ? route('admin.users.update',$user) : route('admin.users.store') }}">@csrf @if($user->exists) @method('PUT') @endif<label>Imię i nazwisko<input name="name" value="{{ old('name',$user->name) }}" required>
</label>
<label>E-mail<input type="email" name="email" value="{{ old('email',$user->email) }}" required>
</label>
<label>Rola<select name="role">@foreach(['customer'=>'Klient','moderator'=>'Moderator','admin'=>'Administrator'] as $key=>$label)<option value="{{ $key }}" @selected(old('role',$user->role) === $key)>{{ $label }}</option>@endforeach</select>
</label>
<label>{{ $user->exists ? 'Nowe hasło (puste = bez zmiany)' : 'Hasło' }}<input type="password" name="password" autocomplete="new-password" minlength="8" maxlength="72" @required(!$user->exists)>
</label>
<small>Minimum 8 znaków: mała i duża litera oraz cyfra.</small>
<label>Powtórz hasło<input type="password" name="password_confirmation" autocomplete="new-password" @required(!$user->exists)>
</label>
<button>Zapisz użytkownika</button>
</form>
</div>@endsection
