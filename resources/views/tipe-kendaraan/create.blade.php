@extends('app')

@section('content')
<h4>Tambah Tipe Kendaraan</h4>

<form action="{{ route('tipe-kendaraan.store') }}" method="POST">
@csrf

<div class="mb-3">
    <label>Kode Tipe</label>
    <input type="text" name="kode_tipe" class="form-control"
           value="{{ old('kode_tipe') }}">
    @error('kode_tipe')
        <small class="text-danger">{{ $message }}</small>
    @enderror
</div>

<div class="mb-3">
    <label>Tipe Kendaraan</label>
    <input type="text" name="tipe_kendaraan" class="form-control"
           value="{{ old('tipe_kendaraan') }}">
    @error('tipe_kendaraan')
        <small class="text-danger">{{ $message }}</small>
    @enderror
</div>

<div class="mb-3">
    <label>Deskripsi</label>
    <textarea name="deskripsi_tipe" class="form-control">{{ old('deskripsi_tipe') }}</textarea>
</div>

<button class="btn btn-primary">Simpan</button>
<a href="{{ route('tipe-kendaraan.index') }}" class="btn btn-secondary">Kembali</a>
</form>
@endsection