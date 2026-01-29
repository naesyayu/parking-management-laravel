@extends('app')

@section('content')
<h4>Backup Area Parkir</h4>

@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif

<a href="{{ route('area-parkir.index') }}" class="btn btn-primary mb-3 mt-4">
    ← Kembali
</a>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>No</th>
            <th>Kode Area</th>
            <th>Lokasi</th>
            <th>Foto</th>
            <th>Dihapus</th>
            <th width="160">Aksi</th>
        </tr>
    </thead>
    <tbody>
        @forelse($areas as $item)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $item->kode_area }}</td>
            <td>{{ $item->lokasi }}</td>
            <td>
                @if($item->foto_lokasi)
                    <img src="{{ asset('storage/'.$item->foto_lokasi) }}" width="80">
                @else
                    -
                @endif
            </td>
            <td>{{ $item->deleted_at }}</td>
            <td>
                <form action="{{ route('area-parkir.restore', $item->id_area) }}" method="POST" class="d-inline">
                    @csrf
                    <button class="btn btn-success btn-sm"
                        onclick="return confirm('Restore area ini?')">
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