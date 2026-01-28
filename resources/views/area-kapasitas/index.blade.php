@extends('app')

@section('content')
<h4>Data Kapasitas Area Parkir</h4>

<a href="{{ route('area-kapasitas.create') }}" class="btn btn-primary mb-3 mt-4">
    + Tambah Kapasitas
</a>

<table class="table table-bordered mt-3">
    <thead>
        <tr>
            <th>Area Parkir</th>
            <th>Tipe Kendaraan</th>
            <th>Kapasitas</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $item)
        <tr>
            <td>{{ $item->area->kode_area }}</td>
            <td>{{ $item->tipe->tipe_kendaraan }}</td>
            <td>{{ $item->kapasitas }}</td>
            <td>
                <a href="{{ route('area-kapasitas.edit', $item->id_kapasitas) }}"
                   class="btn btn-warning btn-sm">Edit</a>

                <form action="{{ route('area-kapasitas.destroy', $item->id_kapasitas) }}"
                      method="POST" class="d-inline">
                    @csrf @method('DELETE')
                    <button onclick="return confirm('Hapus data?')"
                            class="btn btn-danger btn-sm">
                        Hapus
                    </button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

@endsection
