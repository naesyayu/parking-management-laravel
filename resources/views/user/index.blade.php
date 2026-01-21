@extends('app')

@section('content')
<div class="d-flex justify-content-between mb-3">
    <h4>Data Users</h4>
    <a href="{{ route('user.create') }}" class="btn btn-primary">Tambah User</a>
</div>

@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif

<table class="table table-bordered">
    <thead>
        <tr>
            <th>Username</th>
            <th>Role</th>
            <th>Status</th>
            <th width="25%">Aksi</th>
        </tr>
    </thead>
    <tbody>
        @foreach($user as $user)
        <tr>
            <td>{{ $user->username }}</td>
            <td>{{ $user->role->role_user ?? '-' }}</td>
            <td>{{ $user->status }}</td>
            <td>
                <a href="{{ route('user.edit', $user) }}" class="btn btn-warning btn-sm">Edit</a>
                <a href="{{ route('user.password.edit', $user) }}" class="btn btn-secondary btn-sm">Ubah Password</a>

                <form action="{{ route('user.destroy', $user) }}" method="POST" class="d-inline">
                    @csrf @method('DELETE')
                    <button class="btn btn-danger btn-sm" onclick="return confirm('Hapus user?')">Hapus</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
