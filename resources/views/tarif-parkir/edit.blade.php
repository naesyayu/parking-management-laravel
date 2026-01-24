@extends('app')

@section('content')
<h4>Edit Tarif Parkir</h4>

<form action="{{ route('tarif-parkir.update', $tarifParkir->id_tarif) }}" method="POST">
    @csrf
    @method('PUT')

    {{-- Durasi Parkir --}}
    <div class="mb-3">
        <label class="form-label">Durasi Parkir</label>
        <select name="id_tarif_detail" class="form-control" required>
            @foreach ($detailParkir as $detail)
                <option value="{{ $detail->id_tarif_detail }}"
                    {{ $detail->id_tarif_detail == $tarifParkir->id_tarif_detail ? 'selected' : '' }}>
                    {{ $detail->jam_min }} - {{ $detail->jam_max }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- Tipe Kendaraan --}}
    <div class="mb-3">
        <label class="form-label">Tipe Kendaraan</label>
        <select name="id_tipe" class="form-control" required>
            @foreach ($tipeKendaraan as $tipe)
                <option value="{{ $tipe->id_tipe }}"
                    {{ $tipe->id_tipe == $tarifParkir->id_tipe ? 'selected' : '' }}>
                    {{ $tipe->tipe_kendaraan }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- Tarif --}}
    <div class="mb-3">
        <label class="form-label">Tarif (Rp)</label>
        <input type="number"
               name="tarif"
               class="form-control"
               value="{{ $tarifParkir->tarif }}"
               required>
    </div>

    <button class="btn btn-success">Update</button>
    <a href="{{ route('tarif-parkir.index') }}" class="btn btn-secondary">
        Kembali
    </a>
</form>
@endsection
