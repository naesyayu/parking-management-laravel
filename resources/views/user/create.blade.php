@extends('app')

@section('content')
<h4>Tambah User</h4>

<form method="POST" action="{{ route('user.store') }}">
@csrf

<div class="mb-3">
    <label>Username</label>
    <input type="text" name="username" class="form-control">
</div>

<div class="mb-3">
    <label>Password</label>
    <input type="password" name="password" class="form-control">
</div>

<div class="mb-3">
    <label>Role</label>
    <select name="id_role" class="form-control">
        @foreach($roles as $role)
            <option value="{{ $role->id_role }}">{{ $role->role_user }}</option>
        @endforeach
    </select>
</div>

<button class="btn btn-primary">Simpan</button>
<a href="{{ route('user.index') }}" class="btn btn-secondary">Kembali</a>
</form>
@endsection
