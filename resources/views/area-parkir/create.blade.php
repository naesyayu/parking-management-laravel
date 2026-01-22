@extends('app')

@section('content')
<h4>Tambah Area Parkir</h4>

<form method="POST" action="{{ route('area-parkir.store') }}" enctype="multipart/form-data">
@csrf

<div class="mb-3">
    <label>Kode Area</label>
    <input type="text" name="kode_area" class="form-control">
</div>

<div class="mb-3">
    <label>Lokasi</label>
    <textarea name="lokasi" class="form-control"></textarea>
</div>

<div class="mb-3">
    <label>Foto Lokasi</label>
    <input type="file" name="foto_lokasi" class="form-control">
</div>

<button class="btn btn-primary">Simpan</button>
<a href="{{ route('area-parkir.index') }}" class="btn btn-secondary">Kembali</a>
</form>
@endsection
