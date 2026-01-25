@extends('app')

@section('content')
<h4>Edit Role</h4>

<form action="{{ route('roles.update', $role) }}" method="POST">
@csrf
@method('PUT')

<div class="mb-3">
    <label>Role User</label>
    <input type="text" name="role_user" class="form-control"
           value="{{ old('role_user', $role->role_user) }}">
    @error('role_user')
        <small class="text-danger">{{ $message }}</small>
    @enderror
</div>

<button class="btn btn-primary">Update</button>
<a href="{{ route('roles.index') }}" class="btn btn-secondary">Kembali</a>
</form>
@endsection