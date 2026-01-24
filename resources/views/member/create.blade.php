@extends('app')

@section('content')
<h4>Tambah Member</h4>

<form method="POST" action="{{ route('member.store') }}">
@csrf

<div class="mb-3">
    <label>Pemilik</label>
    <select name="id_pemilik" class="form-control">
        @foreach($pemiliks as $p)
            <option value="{{ $p->id_pemilik }}">{{ $p->nama }}</option>
        @endforeach
    </select>
</div>

<div class="mb-3">
    <label>Level Member</label>
    <select name="id_level" class="form-control">
        @foreach($levels as $l)
            <option value="{{ $l->id_level }}">{{ $l->nama_level }}</option>
        @endforeach
    </select>
</div>

<div class="mb-3">
    <label>Berlaku Mulai</label>
    <input type="date" name="berlaku_mulai" class="form-control">
</div>

<div class="mb-3">
    <label>Berlaku Hingga</label>
    <input type="date" name="berlaku_hingga" class="form-control">
</div>

<div class="mb-3">
    <label>Status</label>
    <select name="status" class="form-control">
        <option value="aktif">Aktif</option>
        <option value="expired">Expired</option>
    </select>
</div>

<button class="btn btn-primary">Simpan</button>
<a href="{{ route('member.index') }}" class="btn btn-secondary">Kembali</a>
</form>
@endsection
