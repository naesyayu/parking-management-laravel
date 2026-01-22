@extends('app')

@section('content')
<h4>Tambah Kapasitas Area</h4>

<form method="POST" action="{{ route('area-kapasitas.store') }}">
@csrf

<div class="mb-3">
    <label>Area Parkir</label>
    <select name="id_area" class="form-control">
        @foreach($areas as $area)
            <option value="{{ $area->id_area }}">{{ $area->kode_area }}</option>
        @endforeach
    </select>
</div>

<div class="mb-3">
    <label>Tipe Kendaraan</label>
    <select name="id_tipe" class="form-control">
        @foreach($tipes as $tipe)
            <option value="{{ $tipe->id_tipe }}">{{ $tipe->tipe_kendaraan }}</option>
        @endforeach
    </select>
</div>

<div class="mb-3">
    <label>Kapasitas</label>
    <input type="number" name="kapasitas" class="form-control">
</div>

<button class="btn btn-primary">Simpan</button>
<a href="{{ route('area-kapasitas.index') }}" class="btn btn-secondary">Kembali</a>
</form>
@endsection
