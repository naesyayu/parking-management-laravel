@extends('app')

@section('content')
<h4>Data Metode Pembayaran</h4>

@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif

<table class="table table-bordered mt-4">
    <thead>
        <tr>
            <th>No</th>
            <th>Metode Pembayaran</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        @foreach($metodePembayaran as $item)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $item->metode_bayar }}</td>
            <td>
                <a href="{{ route('metode-pembayaran.edit', $item) }}"
                   class="btn btn-warning btn-sm">
                    Edit
                </a>

                <form action="{{ route('metode-pembayaran.destroy', $item) }}"
                      method="POST" class="d-inline">
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

<a href="{{ route('metode-pembayaran.create') }}"
   class="btn btn-primary mt-3">
    + Tambah Metode Pembayaran
</a>
@endsection