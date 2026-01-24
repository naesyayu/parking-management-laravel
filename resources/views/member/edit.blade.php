@extends('app')

@section('content')
<h4>Edit Member</h4>

<form method="POST"
      action="{{ route('member.update', $member->id_member) }}">
@csrf @method('PUT')

<div class="mb-3">
    <label>Pemilik</label>
    <select name="id_pemilik" class="form-control">
        @foreach($pemiliks as $p)
            <option value="{{ $p->id_pemilik }}"
                {{ $member->id_pemilik == $p->id_pemilik ? 'selected' : '' }}>
                {{ $p->nama }}
            </option>
        @endforeach
    </select>
</div>

<div class="mb-3">
    <label>Level Member</label>
    <select name="id_level" class="form-control">
        @foreach($levels as $l)
            <option value="{{ $l->id_level }}"
                {{ $member->id_level == $l->id_level ? 'selected' : '' }}>
                {{ $l->nama_level }}
            </option>
        @endforeach
    </select>
</div>

<div class="mb-3">
    <label>Berlaku Mulai</label>
    <input type="date" name="berlaku_mulai"
           value="{{ $member->berlaku_mulai }}"
           class="form-control">
</div>

<div class="mb-3">
    <label>Berlaku Hingga</label>
    <input type="date" name="berlaku_hingga"
           value="{{ $member->berlaku_hingga }}"
           class="form-control">
</div>

<div class="mb-3">
    <label>Status</label>
    <select name="status" class="form-control">
        <option value="aktif" {{ $member->status=='aktif'?'selected':'' }}>
            Aktif
        </option>
        <option value="expired" {{ $member->status=='expired'?'selected':'' }}>
            Expired
        </option>
    </select>
</div>

<button class="btn btn-primary">Update</button>
<a href="{{ route('member.index') }}" class="btn btn-secondary">Kembali</a>
</form>
@endsection
