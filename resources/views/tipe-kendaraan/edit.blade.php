@extends('app')

@section('content')
<h4>Edit Tipe Kendaraan</h4>

<form action="{{ route('tipe-kendaraan.update', $tipe_kendaraan) }}" method="POST">
@csrf
@method('PUT')

<div class="mb-3">
    <label>Kode Tipe</label>
    <input type="text" name="kode_tipe" class="form-control"
        value="{{ old('kode_tipe', $tipe_kendaraan->kode_tipe) }}">
    @error('kode_tipe')
        <small class="text-danger">{{ $message }}</small>
    @enderror
</div>

<div class="mb-3">
    <label>Tipe Kendaraan</label>
    <input type="text" name="tipe_kendaraan" class="form-control"
        value="{{ old('tipe_kendaraan', $tipe_kendaraan->tipe_kendaraan) }}">
    @error('tipe_kendaraan')
        <small class="text-danger">{{ $message }}</small>
    @enderror
</div>

<div class="mb-3">
    <label>Deskripsi</label>
    <textarea name="deskripsi_tipe" class="form-control">{{ old('deskripsi_tipe', $tipe_kendaraan->deskripsi_tipe) }}</textarea>
</div>

<button class="btn btn-primary">Update</button>
<a href="{{ route('tipe-kendaraan.index') }}" class="btn btn-secondary">Kembali</a>
</form>
@endsection