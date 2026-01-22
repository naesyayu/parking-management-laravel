@extends('app')

@section('content')
<h4>Edit Kendaraan</h4>

<form method="POST"
      action="{{ route('data-kendaraan.update', $data_kendaraan->id_kendaraan) }}">
@csrf 
@method('PUT')

<div class="mb-3">
    <label>Plat Nomor</label>
    <input type="text" name="plat_nomor"
           value="{{ $data_kendaraan->plat_nomor }}"
           class="form-control">
</div>

<div class="mb-3">
    <label>Pemilik</label>
    <select name="id_pemilik" class="form-control">
        <option value="">-- Tanpa Pemilik --</option>
        @foreach($pemiliks as $p)
            <option value="{{ $p->id_pemilik }}"
                {{ $data_kendaraan->id_pemilik == $p->id_pemilik ? 'selected' : '' }}>
                {{ $p->nama }}
            </option>
        @endforeach
    </select>
</div>

<div class="mb-3">
    <label>Tipe Kendaraan</label>
    <select name="id_tipe" class="form-control">
        @foreach($tipes as $t)
            <option value="{{ $t->id_tipe }}"
                {{ $data_kendaraan->id_tipe == $t->id_tipe ? 'selected' : '' }}>
                {{ $t->tipe_kendaraan }}
            </option>
        @endforeach
    </select>
</div>

<div class="mb-3">
    <label>Status</label>
    <select name="status" class="form-control">
        <option value="aktif" {{ $data_kendaraan->status=='aktif'?'selected':'' }}>
            Aktif
        </option>
        <option value="nonaktif" {{ $data_kendaraan->status=='nonaktif'?'selected':'' }}>
            Nonaktif
        </option>
    </select>
</div>

<button class="btn btn-primary">Update</button>
<a href="{{ route('data-kendaraan.index') }}" class="btn btn-secondary">Kembali</a>
</form>
@endsection
