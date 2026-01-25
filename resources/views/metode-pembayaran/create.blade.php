@extends('app')

@section('content')
<h4>Tambah Metode Pembayaran</h4>

<form action="{{ route('metode-pembayaran.store') }}" method="POST">
@csrf

<div class="mb-3">
    <label>Metode Pembayaran</label>
    <input type="text" name="metode_bayar"
           class="form-control"
           value="{{ old('metode_bayar') }}">
    @error('metode_bayar')
        <small class="text-danger">{{ $message }}</small>
    @enderror
</div>

<button class="btn btn-primary">Simpan</button>
<a href="{{ route('metode-pembayaran.index') }}"
   class="btn btn-secondary">Kembali</a>
</form>
@endsection