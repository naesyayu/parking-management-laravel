@extends('app')

@section('content')
<h4>Backup Data Kendaraan</h4>

@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif

<a href="{{ route('data-kendaraan.index') }}" 
   class="btn btn-primary mb-3 mt-4">
    ← Kembali
</a>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>No</th>
            <th>Plat Nomor</th>
            <th>Pemilik</th>
            <th>Tipe</th>
            <th>Status</th>
            <th>Dihapus</th>
            <th width="120">Aksi</th>
        </tr>
    </thead>
    <tbody>
        @forelse($kendaraans as $item)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $item->plat_nomor }}</td>
            <td>{{ $item->pemilik->nama ?? '-' }}</td>
            <td>{{ $item->tipe->nama_tipe ?? '-' }}</td>
            <td>{{ $item->status }}</td>
            <td>{{ $item->deleted_at }}</td>
            <td>
                <form action="{{ route('data-kendaraan.restore', $item->id_kendaraan) }}"
                      method="POST">
                    @csrf
                    <button class="btn btn-success btn-sm"
                        onclick="return confirm('Restore kendaraan ini?')">
                        Restore
                    </button>
                </form>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="7" class="text-center">
                Tidak ada data terhapus
            </td>
        </tr>
        @endforelse
    </tbody>
</table>
@endsection