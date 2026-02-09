@extends('app')

@section('content')
<div class="container-fluid">
    <h4>📍 Kapasitas Area Parkir</h4>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <a href="{{ route('area-kapasitas.create') }}" class="btn btn-primary mb-3 mt-4">
        <i class="fas fa-plus"></i> Tambah Kapasitas Area
    </a>

    {{-- GROUPED BY AREA --}}
    @forelse($areas as $area)
    <div class="card shadow-sm mb-3">
        <div class="card-header bg-gradient-primary text-white d-flex justify-content-between align-items-center">
            <div>
                <h5 class="mb-0">
                    <i class="fas fa-map-marker-alt"></i> {{ $area->lokasi }}
                </h5>
                <small class="opacity-75">Kode: {{ $area->kode_area }}</small>
            </div>
            <div>
                <a href="{{ route('area-kapasitas.edit', $area->id_area) }}" 
                   class="btn btn-warning btn-sm">
                    <i class="fas fa-edit"></i> Edit
                </a>
                <form action="{{ route('area-kapasitas.destroy', $area->id_area) }}" 
                      method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-danger btn-sm"
                        onclick="return confirm('Hapus semua kapasitas area ini?')">
                        <i class="fas fa-trash"></i> Hapus
                    </button>
                </form>
            </div>
        </div>
        <div class="card-body">
            @if($area->kapasitas->isEmpty())
                <div class="alert alert-warning mb-0">
                    <i class="fas fa-exclamation-triangle"></i> 
                    Belum ada kapasitas untuk area ini
                </div>
            @else
                <div class="row">
                    @foreach($area->kapasitas as $kap)
                    <div class="col-md-4 mb-2">
                        <div class="d-flex justify-content-between align-items-center border rounded p-3 bg-light">
                            <div>
                                <span class="badge bg-info">{{ $kap->tipe->tipe_kendaraan }}</span>
                            </div>
                            <div>
                                <span class="badge bg-{{ $kap->kapasitas > 10 ? 'success' : 'warning' }} fs-6">
                                    {{ $kap->kapasitas }} slot
                                </span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                
                {{-- Summary --}}
                <div class="mt-3 pt-3 border-top">
                    <strong>Total: {{ $area->kapasitas->count() }} tipe kendaraan | 
                    Total Slot: {{ $area->kapasitas->sum('kapasitas') }}</strong>
                </div>
            @endif
        </div>
    </div>
    @empty
    <div class="alert alert-info">
        <i class="fas fa-info-circle"></i> Belum ada area parkir
    </div>
    @endforelse
</div>

<style>
.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}
</style>
@endsection