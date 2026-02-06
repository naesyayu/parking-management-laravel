@extends('app')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-gradient-green text-white">
                    <h4 class="mb-0"><i class="fas fa-database me-2"></i>Master Data Parkir</h4>
                </div>
                <div class="card-body">
                    
                    <!-- Tab Navigation -->
                    <ul class="nav nav-tabs mb-4" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" href="#tarif-tab">
                                <i class="fas fa-money-bill-wave me-1"></i> Tarif Parkir
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#area-tab">
                                <i class="fas fa-map-marked-alt me-1"></i> Area & Kapasitas
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#tipe-tab">
                                <i class="fas fa-car me-1"></i> Tipe Kendaraan
                            </a>
                        </li>
                    </ul>

                    <!-- Tab Content -->
                    <div class="tab-content">
                        
                        <!-- Tab 1: Tarif Parkir -->
                        <div id="tarif-tab" class="tab-pane fade show active">
                            <h5 class="mb-3">Daftar Tarif Parkir Berdasarkan Tipe Kendaraan</h5>
                            
                            @foreach($tipeKendaraan as $tipe)
                            <div class="card mb-3">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">
                                        <i class="fa-solid fa-car-tunnel me-2"></i>
                                        <strong>{{ $tipe->tipe_kendaraan }}</strong> 
                                        <span class="badge bg-secondary">{{ $tipe->kode_tipe }}</span>
                                    </h6>
                                    <small class="text-muted">{{ $tipe->deskripsi_tipe }}</small>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover">
                                            <thead class="table-dark">
                                                <tr>
                                                    <th>Durasi Parkir</th>
                                                    <th>Tarif</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($tipe->tarifParkir as $tarif)
                                                <tr>
                                                    <td>
                                                        <i class="far fa-clock me-1"></i>
                                                        {{ $tarif->detailParkir->jam_min }} - {{ $tarif->detailParkir->jam_max }} Jam
                                                    </td>
                                                    <td>
                                                        <strong class="text-success">Rp {{ number_format($tarif->tarif, 0, ',', '.') }}</strong>
                                                    </td>
                                                </tr>
                                                @empty
                                                <tr>
                                                    <td colspan="2" class="text-center text-muted">
                                                        Belum ada tarif untuk tipe kendaraan ini
                                                    </td>
                                                </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>

                        <!-- Tab 2: Area & Kapasitas -->
                        <div id="area-tab" class="tab-pane fade">
                            <h5 class="mb-3">Kapasitas Area Parkir per Tipe Kendaraan</h5>
                            
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Kode Area</th>
                                            <th>Lokasi</th>
                                            <th>Tipe Kendaraan</th>
                                            <th>Kapasitas</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($areaParkir as $area)
                                            @foreach($area->kapasitas as $kap)
                                            <tr>
                                                <td><span class="badge bg-info">{{ $area->kode_area }}</span></td>
                                                <td>
                                                    <i class="fas fa-map-marker-alt me-1 text-danger"></i>
                                                    {{ $area->lokasi }}
                                                </td>
                                                <td>{{ $kap->tipe->tipe_kendaraan }}</td>
                                                <td>
                                                    <strong class="text-primary">{{ $kap->kapasitas }}</strong> kendaraan
                                                </td>
                                            </tr>
                                            @endforeach
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Tab 3: Tipe Kendaraan -->
                        <div id="tipe-tab" class="tab-pane fade">
                            <h5 class="mb-3">Daftar Tipe Kendaraan</h5>
                            
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Kode Tipe</th>
                                            <th>Nama Tipe</th>
                                            <th>Deskripsi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($tipeKendaraan as $tipe)
                                        <tr>
                                            <td><span class="badge bg-secondary">{{ $tipe->kode_tipe }}</span></td>
                                            <td><strong>{{ $tipe->tipe_kendaraan }}</strong></td>
                                            <td>{{ $tipe->deskripsi_tipe }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .nav-tabs .nav-link {
        color: #495057;
        font-weight: 500;
    }
    .nav-tabs .nav-link.active {
        color: #0d6efd;
        font-weight: 600;
    }
    .card {
        border-radius: 8px;
    }
    .table th {
        font-weight: 600;
    }
    .bg-gradient-green {
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        color: white;
    }
</style>
@endsection