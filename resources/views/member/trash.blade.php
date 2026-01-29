@extends('app')

@section('content')
<h4>Backup Data Member</h4>

@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif

<a href="{{ route('member.index') }}" class="btn btn-primary mb-3 mt-4">
    ← Kembali
</a>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>No</th>
            <th>Pemilik</th>
            <th>Level</th>
            <th>Status</th>
            <th>Dihapus</th>
            <th width="120">Aksi</th>
        </tr>
    </thead>
    <tbody>
        @forelse($members as $item)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $item->pemilik->nama ?? '-' }}</td>
            <td>{{ $item->level->nama_level ?? '-' }}</td>
            <td>{{ $item->status }}</td>
            <td>{{ $item->deleted_at }}</td>
            <td>
                <form action="{{ route('member.restore', $item->id_member) }}" method="POST">
                    @csrf
                    <button class="btn btn-success btn-sm"
                        onclick="return confirm('Restore member ini?')">
                        Restore
                    </button>
                </form>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="6" class="text-center">
                Tidak ada data terhapus
            </td>
        </tr>
        @endforelse
    </tbody>
</table>
@endsection