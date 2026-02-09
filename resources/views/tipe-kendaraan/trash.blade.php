@extends('app')

@section('content')
<h4>Backup Data Tipe Kendaraan</h4>

@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif

<a href="{{ route('tipe-kendaraan.index') }}"
   class="btn btn-primary mb-3 mt-4">
    ← Kembali
</a>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>No</th>
            <th>Kode Tipe</th>
            <th>Tipe Kendaraan</th>
            <th>Deskripsi</th>
            <th>Dihapus</th>
            <th width="120">Aksi</th>
        </tr>
    </thead>
    <tbody>
        @forelse($tipeKendaraan as $item)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $item->kode_tipe }}</td>
            <td>{{ $item->tipe_kendaraan }}</td>
            <td>{{ $item->deskripsi_tipe ?? '-' }}</td>
            <td>{{ $item->deleted_at }}</td>
            <td>
                <form action="{{ route('tipe-kendaraan.restore', $item->id_tipe) }}"
                      method="POST">
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
            <td colspan="6" class="text-center">
                Tidak ada data terhapus
            </td>
        </tr>
        @endforelse
    </tbody>
</table>
@endsection
