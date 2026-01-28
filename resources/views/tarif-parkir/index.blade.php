@extends('app')

@section('content')
<h4>Tarif Parkir</h4>

<a href="{{ route('tarif-parkir.create') }}" class="btn btn-primary mb-3 mt-4">
    + Tambah Tarif
</a>

<table class="table table-bordered mt-3">
    <thead>
        <tr>
            <th>No</th>
            <th>Durasi</th>
            <th>Tipe Kendaraan</th>
            <th>Tarif</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($tarifParkir as $item)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>
                {{ $item->detailParkir->jam_min }}
                -
                {{ $item->detailParkir->jam_max }}
            </td>
            <td>{{ $item->tipeKendaraan->tipe_kendaraan }}</td>
            <td>Rp {{ number_format($item->tarif, 0, ',', '.') }}</td>
            <td>
                <a href="{{ route('tarif-parkir.edit', $item->id_tarif) }}"
                   class="btn btn-warning btn-sm">Edit</a>

                <form action="{{ route('tarif-parkir.destroy', $item->id_tarif) }}"
                      method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
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
