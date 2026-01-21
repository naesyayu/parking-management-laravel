@extends('app')

@section('content')
<h4>Edit Pemilik</h4>

<form method="POST" action="{{ route('pemilik.update', $pemilik) }}">
    @csrf
    @method('PUT')

    <div class="mb-3">
        <label>Nama</label>
        <input type="text" name="nama"
            value="{{ $pemilik->nama }}"
            class="form-control" required>
    </div>

    <div class="mb-3">
        <label>No HP</label>
        <input type="text" name="no_hp"
            value="{{ $pemilik->no_hp }}"
            class="form-control" required>
    </div>

    <div class="mb-3">
        <label>Alamat</label>
        <textarea name="alamat" class="form-control" required>{{ $pemilik->alamat }}</textarea>
    </div>

    <button class="btn btn-primary">Update</button>
    <a href="{{ route('pemilik.index') }}" class="btn btn-secondary">Kembali</a>
</form>
@endsection
