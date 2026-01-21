@extends('app')

@section('content')
<h4>Tambah Pemilik</h4>

<form method="POST" action="{{ route('pemilik.store') }}">
    @csrf

    <div class="mb-3">
        <label>Nama</label>
        <input type="text" name="nama" class="form-control" required>
    </div>

    <div class="mb-3">
        <label>No HP</label>
        <input type="text" name="no_hp" class="form-control" required>
    </div>

    <div class="mb-3">
        <label>Alamat</label>
        <textarea name="alamat" class="form-control" required></textarea>
    </div>

    <button class="btn btn-primary">Simpan</button>
    <a href="{{ route('pemilik.index') }}" class="btn btn-secondary">Kembali</a>
</form>
@endsection
