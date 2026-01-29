@extends('app')

@section('content')
<h4>Backup Metode Pembayaran</h4>

@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif

<a href="{{ route('metode-pembayaran.index') }}" class="btn btn-primary mb-3 mt-4">
    ← Kembali
</a>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>No</th>
            <th>Metode Bayar</th>
            <th>Dihapus Pada</th>
            <th width="150">Aksi</th>
        </tr>
    </thead>
    <tbody>
        @forelse($metodePembayaran as $item)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $item->metode_bayar }}</td>
            <td>{{ $item->deleted_at }}</td>
            <td>
                <form action="{{ route('metode-pembayaran.restore', $item->id_metode) }}" method="POST">
                    @csrf
                    <button class="btn btn-success btn-sm"
                        onclick="return confirm('Restore data ini?')">
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