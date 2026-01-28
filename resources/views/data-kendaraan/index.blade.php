@extends('app')

@section('content')
<h4>Data Kendaraan</h4>

<a href="{{ route('data-kendaraan.create') }}" class="btn btn-primary mb-3 mt-4">
    + Tambah Kendaraan
</a>

<table class="table table-bordered mt-3">
    <thead>
        <tr>
            <th>No</th>
            <th>Plat Nomor</th>
            <th>Pemilik</th>
            <th>Tipe Kendaraan</th>
            <th>Status</th>
            <th width="150">Aksi</th>
        </tr>
    </thead>
    <tbody>
        @foreach($kendaraans as $k)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $k->plat_nomor }}</td>
            <td>{{ $k->pemilik?->nama ?? '-' }}</td>
            <td>{{ $k->tipe->tipe_kendaraan }}</td>
            <td>{{ $k->status }}</td>
            <td>
                <a href="{{ route('data-kendaraan.edit', $k->id_kendaraan) }}"
                   class="btn btn-warning btn-sm">Edit</a>

                <form action="{{ route('data-kendaraan.destroy', $k->id_kendaraan) }}"
                      method="POST" class="d-inline">
                    @csrf @method('DELETE')
                    <button class="btn btn-danger btn-sm"
                            onclick="return confirm('Hapus data?')">
                        Hapus
                    </button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

@endsection
