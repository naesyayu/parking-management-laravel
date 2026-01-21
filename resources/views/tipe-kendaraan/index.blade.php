@extends('app')

@section('content')
<h4>Data Tipe Kendaraan</h4>

@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif

<table class="table table-bordered mt-4">
    <thead>
        <tr>
            <th>No</th>
            <th>Tipe Kendaraan</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        @foreach($tipeKendaraan as $item)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $item->tipe_kendaraan }}</td>
            <td>
                <a href="{{ route('tipe-kendaraan.edit', $item) }}" class="btn btn-warning btn-sm">
                    Edit
                </a>

                <form action="{{ route('tipe-kendaraan.destroy', $item) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-danger btn-sm"
                        onclick="return confirm('Yakin hapus data?')">
                        Hapus
                    </button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

<a href="{{ route('tipe-kendaraan.create') }}" class="btn btn-primary mb-3 mt-3">
    + Tambah Tipe Kendaraan
</a>

@endsection
