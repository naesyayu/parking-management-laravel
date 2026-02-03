@extends('app')

@section('content')
    <h4 class="mb-3">Edit Detail Durasi Parkir</h4>

    <form action="{{ route('detail-parkir.update', $detailParkir->id_tarif_detail) }}"
          method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label class="form-label">Jam Minimal</label>
            <input type="number" step="0.01"
                   name="jam_min"
                   class="form-control @error('jam_min') is-invalid @enderror"
                   value="{{ old('jam_min', $detailParkir->jam_min) }}">
            @error('jam_min')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Jam Maksimal</label>
            <input type="number" step="0.01"
                   name="jam_max"
                   class="form-control @error('jam_max') is-invalid @enderror"
                   value="{{ old('jam_max', $detailParkir->jam_max) }}">
            @error('jam_max')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button class="btn btn-warning">Update</button>
        <a href="{{ route('detail-parkir.index') }}" class="btn btn-secondary mb-3">
            ← Kembali
        </a>

    </form>
@endsection
