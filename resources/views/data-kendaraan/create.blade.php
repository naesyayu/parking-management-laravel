@extends('app')

@section('content')
<h4>Tambah Kendaraan</h4>

<form method="POST" action="{{ route('data-kendaraan.store') }}">
@csrf

<div class="mb-3">
    <label>Plat Nomor</label>
    <input type="text" name="plat_nomor" class="form-control" required>
</div>

<div class="mb-3">
    <label>Pemilik (Opsional)</label>
    <select name="id_pemilik" class="form-control">
        <option value="">-- Tanpa Pemilik --</option>
        @foreach($pemiliks as $p)
            <option value="{{ $p->id_pemilik }}">{{ $p->nama }}</option>
        @endforeach
    </select>
</div>

<div class="mb-3">
    <label>Tipe Kendaraan</label>
    <select name="id_tipe" class="form-control" required>
        @foreach($tipes as $t)
            <option value="{{ $t->id_tipe }}">{{ $t->tipe_kendaraan }}</option>
        @endforeach
    </select>
</div>

<div class="mb-3">
    <label>Status</label>
    <select name="status" class="form-control">
        <option value="aktif">Aktif</option>
        <option value="nonaktif">Nonaktif</option>
    </select>
</div>

<button class="btn btn-primary">Simpan</button>
<a href="{{ route('data-kendaraan.index') }}" class="btn btn-secondary">Kembali</a>
</form>
@endsection
