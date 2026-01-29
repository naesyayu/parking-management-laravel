@extends('app')

@section('content')
<h4>Backup Role (Soft Delete)</h4>

@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif

<a href="{{ route('roles.index') }}" class="btn btn-primary mb-3 mt-4">
    ← Kembali
</a>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>No</th>
            <th>Role</th>
            <th>Dihapus Pada</th>
            <th width="150">Aksi</th>
        </tr>
    </thead>
    <tbody>
        @forelse($roles as $role)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $role->role_user }}</td>
            <td>{{ $role->deleted_at }}</td>
            <td>
                <form action="{{ route('roles.restore', $role->id_role) }}" method="POST">
                    @csrf
                    <button class="btn btn-success btn-sm"
                        onclick="return confirm('Restore role ini?')">
                        Restore
                    </button>
                </form>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="4" class="text-center">
                Tidak ada data terhapus
            </td>
        </tr>
        @endforelse
    </tbody>
</table>
@endsection