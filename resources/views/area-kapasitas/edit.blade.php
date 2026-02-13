@extends('app')

@section('content')
<div class="container-fluid">
    <h4>Edit Kapasitas Area: {{ $area->nama_area }}</h4>

    {{-- WARNING IF ACTIVE TRANSACTIONS --}}
    @php
        $hasActiveTransactions = false;
        $blockedTypes = [];
        $warningTypes = [];
        
        foreach($activeTransactions as $idTipe => $count) {
            if($count > 0) {
                $hasActiveTransactions = true;
                $tipe = $tipes->firstWhere('id_tipe', $idTipe);
                $blockedTypes[$idTipe] = $count;
                $warningTypes[] = $tipe->tipe_kendaraan . " (minimal {$count} slot karena ada {$count} kendaraan parkir)";
            }
        }
    @endphp

    @if($hasActiveTransactions)
        <div class="alert alert-warning alert-dismissible fade show">
            <h5 class="alert-heading">
                <i class="fas fa-exclamation-triangle"></i> Perhatian!
            </h5>
            <p class="mb-0">
                <strong>Beberapa tipe kendaraan memiliki kendaraan yang sedang parkir:</strong>
            </p>
            <ul class="mb-0 mt-2">
                @foreach($warningTypes as $warning)
                    <li>{{ $warning }}</li>
                @endforeach
            </ul>
            <hr>
            <small class="mb-0">
                <i class="fas fa-info-circle"></i> 
                Kapasitas <strong>tetap bisa diedit</strong>, namun tidak boleh lebih kecil dari jumlah kendaraan yang sedang parkir.
            </small>
            <button class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form method="POST" action="{{ route('area-kapasitas.update', $area->id_area) }}" id="formKapasitas">
    @csrf
    @method('PUT')

    {{-- AREA INFO --}}
    <div class="card shadow-sm mb-3">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">
                <i class="fas fa-map-marker-alt"></i> {{ $area->nama_area }}
            </h5>
            <small>Kode: {{ $area->kode_area }} | Lokasi: {{ $area->lokasi }}</small>
        </div>
    </div>

    {{-- INPUT KAPASITAS PER TIPE --}}
    <div class="card shadow-sm mb-3">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0">Kapasitas per Tipe Kendaraan</h5>
        </div>
        <div class="card-body">
            <div class="row">
                @foreach($tipes as $tipe)
                @php
                    $activeCount = $activeTransactions[$tipe->id_tipe] ?? 0;
                    $hasActiveVehicles = $activeCount > 0;
                    $currentKapasitas = $existingKapasitas[$tipe->id_tipe] ?? 0;
                @endphp
                
                <div class="col-md-4 mb-3">
                    <div class="border rounded p-3 {{ $hasActiveVehicles ? 'bg-warning bg-opacity-10' : 'bg-white' }}">
                        <label class="form-label fw-bold">
                            <i class="fas fa-{{ $tipe->tipe_kendaraan == 'Motor' ? 'motorcycle' : ($tipe->tipe_kendaraan == 'Mobil' ? 'car' : 'bus') }}"></i>
                            {{ $tipe->tipe_kendaraan }}
                            
                            {{-- STATUS BADGE --}}
                            @if($hasActiveVehicles)
                                <span class="badge bg-warning text-dark ms-2">
                                    <i class="fas fa-exclamation-triangle"></i> {{ $activeCount }} Parkir
                                </span>
                            @else
                                <span class="badge bg-success ms-2">
                                    <i class="fas fa-check-circle"></i> Kosong
                                </span>
                            @endif
                        </label>
                        
                        <div class="input-group">
                            <input 
                                type="number" 
                                name="kapasitas[{{ $tipe->id_tipe }}]" 
                                class="form-control form-control-lg" 
                                value="{{ old('kapasitas.'.$tipe->id_tipe, $currentKapasitas) }}"
                                min="{{ $activeCount }}"
                                placeholder="0"
                                data-min="{{ $activeCount }}"
                                data-tipe="{{ $tipe->tipe_kendaraan }}"
                            >
                            <span class="input-group-text">slot</span>
                        </div>
                        
                        {{-- INFO TEXT --}}
                        @if($hasActiveVehicles)
                            <small class="text-warning d-block mt-2">
                                <i class="fas fa-info-circle"></i> 
                                <strong>{{ $activeCount }} kendaraan sedang parkir</strong>
                                <br>
                                Kapasitas minimal: <strong>{{ $activeCount }}</strong> slot
                                <br>
                                <em>Anda bisa menambah slot, tetapi tidak bisa kurang dari {{ $activeCount }}</em>
                            </small>
                        @else
                            @if($currentKapasitas > 0)
                                <small class="text-muted d-block mt-2">
                                    <i class="fas fa-check-circle text-success"></i> 
                                    Kapasitas saat ini: {{ $currentKapasitas }} (Bisa diedit bebas)
                                </small>
                            @else
                                <small class="text-muted d-block mt-2">
                                    <i class="fas fa-info-circle"></i> 
                                    Belum ada kapasitas untuk tipe ini
                                </small>
                            @endif
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- BUTTONS --}}
    <div class="d-grid gap-2">
        <button type="submit" class="btn btn-primary btn-lg">
            <i class="fas fa-save"></i> Update Kapasitas
        </button>
        
        <a href="{{ route('area-kapasitas.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    </form>
</div>

@endsection

@push('scripts')
<script>
// ========================================
// CLIENT-SIDE VALIDATION
// ========================================
document.getElementById('formKapasitas').addEventListener('submit', function(e) {
    let hasError = false;
    let errorMessages = [];
    
    // Check all inputs
    const inputs = this.querySelectorAll('input[type="number"]');
    
    inputs.forEach(input => {
        const value = parseInt(input.value) || 0;
        const minValue = parseInt(input.dataset.min) || 0;
        const tipeName = input.dataset.tipe;
        
        if (value > 0 && value < minValue) {
            hasError = true;
            errorMessages.push(`${tipeName}: Kapasitas tidak boleh kurang dari ${minValue} (ada ${minValue} kendaraan parkir)`);
            input.classList.add('is-invalid');
        } else {
            input.classList.remove('is-invalid');
        }
    });
    
    if (hasError) {
        e.preventDefault();
        
        Swal.fire({
            icon: 'error',
            title: 'Validasi Gagal!',
            html: '<ul style="text-align: left;">' + 
                  errorMessages.map(msg => `<li>${msg}</li>`).join('') + 
                  '</ul>',
            confirmButtonText: 'OK'
        });
        
        return false;
    }
});

// ========================================
// REAL-TIME VALIDATION
// ========================================
document.querySelectorAll('input[type="number"]').forEach(input => {
    input.addEventListener('input', function() {
        const value = parseInt(this.value) || 0;
        const minValue = parseInt(this.dataset.min) || 0;
        
        if (value > 0 && value < minValue) {
            this.classList.add('is-invalid');
        } else {
            this.classList.remove('is-invalid');
        }
    });
});
</script>

<style>
.is-invalid {
    border-color: #dc3545 !important;
}

.bg-warning.bg-opacity-10 {
    background-color: rgba(255, 193, 7, 0.1) !important;
}
</style>
@endpush