@extends('app')

@section('content')
    <h4 class="mb-3">Tambah Detail Durasi Parkir</h4>

    <form action="{{ route('detail-parkir.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label class="form-label">Jam Minimal</label>
            <input type="number" step="0.01"
                   name="jam_min"
                   class="form-control @error('jam_min') is-invalid @enderror"
                   value="{{ old('jam_min') }}">
            @error('jam_min')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Jam Maksimal</label>
            <input type="number" step="0.01"
                   name="jam_max"
                   class="form-control @error('jam_max') is-invalid @enderror"
                   value="{{ old('jam_max') }}">
            @error('jam_max')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button class="btn btn-primary">Simpan</button>
        <a href="{{ route('detail-parkir.index') }}" class="btn btn-secondary">
            ← Kembali
        </a>
    </form>
@endsection
