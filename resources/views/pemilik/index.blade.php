@extends('app')

@section('content')
<h4>Data Pemilik</h4>

@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif

<a href="{{ route('pemilik.create') }}" class="btn btn-primary mb-3 mt-4">
    + Tambah Pemilik
</a>

<a href="{{ route('pemilik.trash') }}" class="btn btn-secondary mb-3 mt-4">
    Backup Data Pemilik
</a>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>No</th>
            <th>Nama</th>
            <th>No HP</th>
            <th>Alamat</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        @foreach($pemilik as $item)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $item->nama }}</td>
            <td>{{ $item->no_hp }}</td>
            <td>{{ $item->alamat }}</td>
            <td>
                <a href="{{ route('pemilik.edit', $item) }}" class="btn btn-warning btn-sm">
                    Edit
                </a>

                <form action="{{ route('pemilik.destroy', $item) }}" method="POST" class="d-inline">
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

@endsection
