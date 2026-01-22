@extends('app')

@section('content')
<h4>Edit Area Parkir</h4>

<form method="POST" action="{{ route('area-parkir.update', $area_parkir->id_area) }}"
      enctype="multipart/form-data">
@csrf @method('PUT')

<div class="mb-3">
    <label>Kode Area</label>
    <input type="text" name="kode_area" value="{{ $area_parkir->kode_area }}" class="form-control">
</div>

<div class="mb-3">
    <label>Lokasi</label>
    <textarea name="lokasi" class="form-control">{{ $area_parkir->lokasi }}</textarea>
</div>

<div class="mb-3">
    <label>Foto Lokasi</label><br>
    @if($area_parkir->foto_lokasi)
        <img src="{{ asset('storage/'.$area_parkir->foto_lokasi) }}" width="150" class="mb-2"><br>
    @endif
    <input type="file" name="foto_lokasi" class="form-control">
</div>

<button class="btn btn-primary">Update</button>
<a href="{{ route('area-parkir.index') }}" class="btn btn-secondary">Kembali</a>
</form>
@endsection
