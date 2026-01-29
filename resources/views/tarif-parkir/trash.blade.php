@extends('app')

@section('content')
<h4>Backup Tarif Parkir (Soft Delete)</h4>

@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif

<a href="{{ route('tarif-parkir.index') }}" class="btn btn-primary mb-3 mt-4">
    ← Kembali
</a>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>No</th>
            <th>Detail Parkir</th>
            <th>Tipe Kendaraan</th>
            <th>Tarif</th>
            <th>Dihapus Pada</th>
            <th width="150">Aksi</th>
        </tr>
    </thead>
    <tbody>
        @forelse($tarifParkir as $item)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $item->detailParkir->nama_detail ?? '-' }}</td>
            <td>{{ $item->tipeKendaraan->nama_tipe ?? '-' }}</td>
            <td>Rp {{ number_format($item->tarif, 0, ',', '.') }}</td>
            <td>{{ $item->deleted_at }}</td>
            <td>
                <form action="{{ route('tarif-parkir.restore', $item->id_tarif) }}" method="POST">
                    @csrf
                    <button class="btn btn-success btn-sm"
                        onclick="return confirm('Restore tarif ini?')">
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