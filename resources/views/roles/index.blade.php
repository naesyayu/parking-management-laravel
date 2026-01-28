@extends('app')

@section('content')
<h4>Data Role</h4>

@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif

<a href="{{ route('roles.create') }}" class="btn btn-primary mb-3 mt-4">
    + Tambah Role
</a>

<table class="table table-bordered mt-3">
    <thead>
        <tr>
            <th>No</th>
            <th>Role User</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        @foreach($roles as $item)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $item->role_user }}</td>
            <td>
                <a href="{{ route('roles.edit', $item) }}" class="btn btn-warning btn-sm">Edit</a>

                <form action="{{ route('roles.destroy', $item) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-danger btn-sm"
                        onclick="return confirm('Yakin hapus data?')">
                        Hapus
                    </button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

@endsection