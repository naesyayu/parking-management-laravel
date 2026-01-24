@extends('app')

@section('content')
<h4>Tambah Tarif Parkir</h4>

<form action="{{ route('tarif-parkir.store') }}" method="POST">
    @csrf

    {{-- Durasi Parkir --}}
    <div class="mb-3">
        <label class="form-label">Durasi Parkir</label>
        <select name="id_tarif_detail" class="form-control" required>
            <option value="">-- Pilih Durasi --</option>
            @foreach ($detailParkir as $detail)
                <option value="{{ $detail->id_tarif_detail }}">
                    {{ $detail->jam_min }} - {{ $detail->jam_max }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- Tipe Kendaraan --}}
    <div class="mb-3">
        <label class="form-label">Tipe Kendaraan</label>
        <select name="id_tipe" class="form-control" required>
            <option value="">-- Pilih Tipe --</option>
            @foreach ($tipeKendaraan as $tipe)
                <option value="{{ $tipe->id_tipe }}">
                    {{ $tipe->tipe_kendaraan }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- Tarif --}}
    <div class="mb-3">
        <label class="form-label">Tarif (Rp)</label>
        <input type="number" name="tarif" class="form-control" required>
    </div>

    <button class="btn btn-primary">Simpan</button>
    <a href="{{ route('tarif-parkir.index') }}" class="btn btn-secondary">
        Kembali
    </a>
</form>
@endsection
