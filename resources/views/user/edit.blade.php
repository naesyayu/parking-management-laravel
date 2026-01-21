@extends('app')

@section('content')
<h4>Edit User</h4>

<form method="POST" action="{{ route('user.update', $user->id_user) }}">
    @csrf
    @method('PUT')

    <div class="mb-3">
        <label>Username</label>
        <input 
            type="text" 
            name="username" 
            value="{{ old('username', $user->username) }}" 
            class="form-control">
    </div>

    <div class="mb-3">
        <label>Role</label>
        <select name="id_role" class="form-control">
            @foreach($roles as $role)
                <option value="{{ $role->id_role }}"
                    {{ old('id_role', $user->id_role) == $role->id_role ? 'selected' : '' }}>
                    {{ $role->role_user }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="mb-3">
        <label>Status</label>
        <select name="status" class="form-control">
            <option value="aktif" {{ old('status', $user->status)=='aktif' ? 'selected' : '' }}>Aktif</option>
            <option value="nonaktif" {{ old('status', $user->status)=='nonaktif' ? 'selected' : '' }}>Nonaktif</option>
        </select>
    </div>

    <button class="btn btn-primary">Update</button>
    <a href="{{ route('user.index') }}" class="btn btn-secondary">Kembali</a>
</form>
@endsection
