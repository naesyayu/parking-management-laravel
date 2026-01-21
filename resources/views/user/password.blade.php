@extends('app')

@section('content')
<h4>Update Password: {{ $user->username }}</h4>

<form method="POST" action="{{ route('user.password.update',$user) }}">
@csrf @method('PUT')

<div class="mb-3">
    <label>Password Baru</label>
    <input type="password" name="password" class="form-control">
</div>

<div class="mb-3">
    <label>Konfirmasi Password</label>
    <input type="password" name="password_confirmation" class="form-control">
</div>

<button class="btn btn-primary">Update Password</button>
<a href="{{ route('user.index') }}" class="btn btn-secondary">Kembali</a>
</form>
@endsection
