@extends('app')

@section('content')
<h4>Tambah Area Parkir</h4>

<form method="POST"
      action="{{ route('area-parkir.store') }}"
      enctype="multipart/form-data">
@csrf

<div class="mb-3">
    <label>Kode Area</label>
    <input type="text"
           name="kode_area"
           class="form-control"
           value="{{ old('kode_area') }}"
           required>
</div>

<div class="mb-3">
    <label>Nama Area</label>
    <input type="text"
           name="nama_area"
           class="form-control"
           value="{{ old('nama_area') }}"
           required>
</div>

<div class="mb-3">
    <label>Lokasi</label>
    <textarea name="lokasi"
              class="form-control"
              rows="3">{{ old('lokasi') }}</textarea>
</div>

<div class="mb-3">
    <label>Foto Lokasi</label>
    <input type="file"
           name="foto_lokasi"
           class="form-control">
</div>

<button class="btn btn-primary">Simpan</button>
<a href="{{ route('area-parkir.index') }}" class="btn btn-secondary">
    Kembali
</a>
</form>
@endsection
