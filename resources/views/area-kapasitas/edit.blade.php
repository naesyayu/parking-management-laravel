@extends('app')

@section('content')
<h4>Edit Kapasitas Area</h4>

<form method="POST"
      action="{{ route('area-kapasitas.update', $area_kapasitas->id_kapasitas) }}">
@csrf @method('PUT')

<div class="mb-3">
    <label>Area Parkir</label>
    <select name="id_area" class="form-control">
        @foreach($areas as $area)
            <option value="{{ $area->id_area }}"
                {{ $area_kapasitas->id_area == $area->id_area ? 'selected' : '' }}>
                {{ $area->kode_area }}
            </option>
        @endforeach
    </select>
</div>

<div class="mb-3">
    <label>Tipe Kendaraan</label>
    <select name="id_tipe" class="form-control">
        @foreach($tipes as $tipe)
            <option value="{{ $tipe->id_tipe }}"
                {{ $area_kapasitas->id_tipe == $tipe->id_tipe ? 'selected' : '' }}>
                {{ $tipe->tipe_kendaraan }}
            </option>
        @endforeach
    </select>
</div>

<div class="mb-3">
    <label>Kapasitas</label>
    <input type="number"
           name="kapasitas"
           value="{{ $area_kapasitas->kapasitas }}"
           class="form-control">
</div>

<button class="btn btn-primary">Update</button>
<a href="{{ route('area-kapasitas.index') }}" class="btn btn-secondary">Kembali</a>
</form>
@endsection
