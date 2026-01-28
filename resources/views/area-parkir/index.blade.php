@extends('app')

@section('content')
<h4>Data Area Parkir</h4>

<a href="{{ route('area-parkir.create') }}" class="btn btn-primary mb-3 mt-4">
   + Tambah Area
</a>

<table class="table table-bordered mt-3">
    <thead>
        <tr>
            <th>Kode Area</th>
            <th>Lokasi</th>
            <th>Foto</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        @foreach($areas as $area)
        <tr>
            <td>{{ $area->kode_area }}</td>
            <td>{{ $area->lokasi }}</td>
            <td>
                @if($area->foto_lokasi)
                    <img src="{{ asset('storage/'.$area->foto_lokasi) }}" width="120">
                @else
                    <em>Tidak ada</em>
                @endif
            </td>
            <td>
                <a href="{{ route('area-parkir.edit', $area->id_area) }}" class="btn btn-warning btn-sm">Edit</a>

                <form action="{{ route('area-parkir.destroy', $area->id_area) }}"
                      method="POST" class="d-inline">
                    @csrf @method('DELETE')
                    <button onclick="return confirm('Hapus data?')" class="btn btn-danger btn-sm">
                        Hapus
                    </button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

@endsection
