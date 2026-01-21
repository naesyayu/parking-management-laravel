@extends('app')

@section('content')
<h4>Tambah Tipe Kendaraan</h4>

<form action="{{ route('tipe-kendaraan.store') }}" method="POST">
@csrf

<div class="mb-3">
    <label>Tipe Kendaraan</label>
    <input type="text" name="tipe_kendaraan" class="form-control"
        value="{{ old('tipe_kendaraan') }}">
    @error('tipe_kendaraan')
        <small class="text-danger">{{ $message }}</small>
    @enderror
</div>

<button class="btn btn-primary">Simpan</button>
<a href="{{ route('tipe-kendaraan.index') }}" class="btn btn-secondary">Kembali</a>
</form>
@endsection
