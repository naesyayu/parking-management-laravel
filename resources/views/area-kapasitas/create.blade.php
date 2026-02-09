@extends('app')

@section('content')
<div class="container-fluid">
    <h4>Tambah Kapasitas Area Parkir</h4>

    <form method="POST" action="{{ route('area-kapasitas.store') }}">
    @csrf

    {{-- SELECT AREA --}}
    <div class="card shadow-sm mb-3">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">1. Pilih Area Parkir</h5>
        </div>
        <div class="card-body">
            <select name="id_area" id="areaSelect" class="form-select form-select-lg" required>
                <option value="">-- Pilih Area --</option>
                @foreach($areas as $area)
                    <option value="{{ $area->id_area }}" {{ old('id_area') == $area->id_area ? 'selected' : '' }}>
                        {{ $area->kode_area }} - {{ $area->lokasi }}
                    </option>
                @endforeach
            </select>
            @error('id_area')
                <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
        </div>
    </div>

    {{-- INPUT KAPASITAS PER TIPE --}}
    <div class="card shadow-sm mb-3">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0">2. Input Kapasitas per Tipe Kendaraan</h5>
        </div>
        <div class="card-body">
            <p class="text-muted">
                <i class="fas fa-info-circle"></i> 
                Masukkan kapasitas untuk setiap tipe kendaraan. Biarkan 0 jika tidak tersedia.
            </p>
            
            <div class="row">
                @foreach($tipes as $tipe)
                <div class="col-md-4 mb-3">
                    <div class="border rounded p-3 bg-light">
                        <label class="form-label fw-bold">
                            <i class="fas fa-{{ $tipe->tipe_kendaraan == 'Motor' ? 'motorcycle' : ($tipe->tipe_kendaraan == 'Mobil' ? 'car' : 'bus') }}"></i>
                            {{ $tipe->tipe_kendaraan }}
                        </label>
                        <div class="input-group">
                            <input 
                                type="number" 
                                name="kapasitas[{{ $tipe->id_tipe }}]" 
                                class="form-control form-control-lg" 
                                value="{{ old('kapasitas.'.$tipe->id_tipe, 0) }}"
                                min="0"
                                placeholder="0"
                            >
                            <span class="input-group-text">slot</span>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            
            @error('kapasitas')
                <div class="text-danger small">{{ $message }}</div>
            @enderror
        </div>
    </div>

    {{-- BUTTONS --}}
    <div class="d-grid gap-2">
        <button type="submit" class="btn btn-primary btn-lg">
            <i class="fas fa-save"></i> Simpan Kapasitas
        </button>
        <a href="{{ route('area-kapasitas.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    </form>
</div>
@endsection