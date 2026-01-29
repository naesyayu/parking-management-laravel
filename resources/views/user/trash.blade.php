@extends('app')

@section('content')
<h4>Backup User (Soft Delete)</h4>

@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif

<a href="{{ route('user.index') }}" class="btn btn-primary mb-3 mt-4">
    ← Kembali
</a>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>Username</th>
            <th>Role</th>
            <th>Status</th>
            <th>Dihapus Pada</th>
            <th width="150">Aksi</th>
        </tr>
    </thead>
    <tbody>
        @forelse($user as $item)
        <tr>
            <td>{{ $item->username }}</td>
            <td>{{ $item->role->role_user ?? '-' }}</td>
            <td>{{ $item->status }}</td>
            <td>{{ $item->deleted_at }}</td>
            <td>
                <form action="{{ route('user.restore', $item->id_user) }}" method="POST">
                    @csrf
                    <button class="btn btn-success btn-sm"
                        onclick="return confirm('Restore user ini?')">
                        Restore
                    </button>
                </form>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="5" class="text-center">
                Tidak ada user terhapus
            </td>
        </tr>
        @endforelse
    </tbody>
</table>
@endsection