@extends('app')

@section('content')
<div class="container-fluid">
    {{-- HEADER --}}
    <div class="row mb-4">
        <div class="col-md-8">
            <h2 class="mb-0">📊 Breakdown Laporan Transaksi</h2>
            <small class="text-muted">Analisis detail pendapatan berdasarkan tipe kendaraan, metode pembayaran, dan occupancy area</small>
        </div>
        <div class="col-md-4 text-end">
            @if($role->isAdmin())
                <form method="GET" action="{{ route('laporan.export') }}" class="d-inline">
                    @foreach(request()->query() as $key => $value)
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endforeach
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-download"></i> Export CSV
                    </button>
                </form>
            @endif
        </div>
    </div>

    {{-- FILTER FORM --}}
    <div class="card mb-3 shadow-sm">
        <div class="card-header bg-light">
            <h5 class="mb-0"><i class="fas fa-filter"></i> Filter & Pencarian</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('laporan.breakdown') }}" class="row g-2">
                <div class="col-md-2">
                    <label class="form-label small text-muted">Plat Nomor</label>
                    <input type="text" name="plat_nomor" class="form-control form-control-sm" 
                           placeholder="Cth: D 1234 AB" value="{{ request('plat_nomor') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label small text-muted">Area</label>
                    <select name="id_area" class="form-select form-select-sm">
                        <option value="">Semua Area</option>
                        @foreach($areas as $area)
                            <option value="{{ $area->id_area }}" {{ request('id_area') == $area->id_area ? 'selected' : '' }}>
                                {{ $area->lokasi }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small text-muted">Tgl Mulai</label>
                    <input type="date" name="start_date" class="form-control form-control-sm" value="{{ request('start_date') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label small text-muted">Tgl Akhir</label>
                    <input type="date" name="end_date" class="form-control form-control-sm" value="{{ request('end_date') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label small text-muted">Metode Bayar</label>
                    <select name="id_metode" class="form-select form-select-sm">
                        <option value="">Semua Metode</option>
                        @foreach($metodes as $metode)
                            <option value="{{ $metode->id_metode }}" {{ request('id_metode') == $metode->id_metode ? 'selected' : '' }}>
                                {{ $metode->metode_bayar }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small text-muted">Tipe Kendaraan</label>
                    <select name="id_tipe" class="form-select form-select-sm">
                        <option value="">Semua Tipe</option>
                        @foreach($tipes as $tipe)
                            <option value="{{ $tipe->id_tipe }}" {{ request('id_tipe') == $tipe->id_tipe ? 'selected' : '' }}>
                                {{ $tipe->tipe_kendaraan }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-12 pt-2 d-flex gap-2">
                    <button type="submit" class="btn btn-sm btn-primary">
                        <i class="fas fa-search"></i> Cari
                    </button>
                    <a href="{{ route('laporan.breakdown') }}" class="btn btn-sm btn-secondary">
                        <i class="fas fa-redo"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- SUMMARY CARDS --}}
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card text-center shadow-sm h-100">
                <div class="card-body">
                    <h6 class="card-title text-muted mb-2">Total Transaksi</h6>
                    <h3 class="mb-0">{{ $summary['total_transaksi'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card text-center shadow-sm border-success h-100">
                <div class="card-body bg-light">
                    <h6 class="card-title text-muted mb-2">Total Pendapatan</h6>
                    <h3 class="text-success mb-0">Rp {{ number_format($summary['total_pendapatan'], 0, ',', '.') }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card text-center shadow-sm border-warning h-100">
                <div class="card-body bg-light">
                    <h6 class="card-title text-muted mb-2">Total Diskon</h6>
                    <h3 class="text-warning mb-0">Rp {{ number_format($summary['total_diskon'], 0, ',', '.') }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card text-center shadow-sm border-info h-100">
                <div class="card-body bg-light">
                    <h6 class="card-title text-muted mb-2">Rata-rata</h6>
                    <h3 class="text-info mb-0">Rp {{ number_format($summary['avg'], 0, ',', '.') }}</h3>
                </div>
            </div>
        </div>
    </div>

    {{-- ========================================= --}}
    {{-- CHARTS SECTION - POLA TRANSAKSI --}}
    {{-- ========================================= --}}
    <div class="row mb-4">
        <div class="col-md-12">
            <h5 class="mb-3"><i class="fas fa-chart-line"></i> Pola Transaksi</h5>
        </div>

        {{-- CHART: Transaksi Per Hari (EXPANDED - Full Width) --}}
        <div class="col-md-12 mb-3">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h6 class="mb-0">
                        <i class="fas fa-calendar"></i> Transaksi Per Hari 
                        <small class="text-muted">(Default: 30 hari terakhir, Max: 90 hari)</small>
                    </h6>
                </div>
                <div class="card-body">
                    <canvas id="chartPerHari" style="height: 400px;"></canvas>
                </div>
            </div>
        </div>

        {{-- CHART: Pie Tipe Kendaraan --}}
        <div class="col-md-6 mb-3">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="fas fa-car"></i> Distribusi Tipe Kendaraan</h6>
                </div>
                <div class="card-body">
                    <canvas id="chartTipeKendaraan"></canvas>
                </div>
            </div>
        </div>

        {{-- CHART: Pie Metode Pembayaran --}}
        <div class="col-md-6 mb-3">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="fas fa-credit-card"></i> Distribusi Metode Pembayaran</h6>
                </div>
                <div class="card-body">
                    <canvas id="chartMetodePembayaran"></canvas>
                </div>
            </div>
        </div>
    </div>

    <hr class="my-4">

    {{-- BREAKDOWN PER TIPE KENDARAAN --}}
    <div class="row mb-4">
        <div class="col-md-12">
            <h5 class="mb-3"><i class="fas fa-chart-pie"></i> Breakdown Per Tipe Kendaraan</h5>
        </div>
        @forelse($breakdownTipe as $item)
        <div class="col-md-4 mb-3">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <h6 class="card-title">{{ $item['tipe'] }}</h6>
                    <table class="table table-sm mb-0">
                        <tr>
                            <td class="text-muted">Unit:</td>
                            <td class="fw-bold">{{ $item['jumlah'] }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Revenue:</td>
                            <td class="fw-bold text-success">Rp {{ number_format($item['revenue'], 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Rata-rata:</td>
                            <td class="text-info">Rp {{ number_format($item['avg'], 0, ',', '.') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        @empty
        <div class="col-md-12">
            <div class="alert alert-info mb-4">Tidak ada data breakdown per tipe kendaraan</div>
        </div>
        @endforelse
    </div>

    <hr class="my-4">

    {{-- BREAKDOWN PER METODE PEMBAYARAN --}}
    <div class="row mb-4">
        <div class="col-md-12">
            <h5 class="mb-3"><i class="fas fa-credit-card"></i> Breakdown Per Metode Pembayaran</h5>
        </div>
        @forelse($breakdownMetode as $item)
        <div class="col-md-4 mb-3">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <h6 class="card-title">{{ $item['metode'] }}</h6>
                    <table class="table table-sm mb-0">
                        <tr>
                            <td class="text-muted">Transaksi:</td>
                            <td class="fw-bold">{{ $item['jumlah'] }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Revenue:</td>
                            <td class="fw-bold text-success">Rp {{ number_format($item['revenue'], 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Rata-rata:</td>
                            <td class="text-info">Rp {{ number_format($item['avg'], 0, ',', '.') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        @empty
        <div class="col-md-12">
            <div class="alert alert-info mb-4">Tidak ada data breakdown per metode pembayaran</div>
        </div>
        @endforelse
    </div>

    <hr class="my-4">

    {{-- OCCUPANCY RATE PER AREA & TIPE --}}
<div class="row mb-4">
    <div class="col-md-12 mb-3">
        <h5><i class="fas fa-parking"></i> Status Slot Parkir Per Area</h5>
        <p class="text-muted small mb-0">Real-time occupancy breakdown per area dan per tipe kendaraan</p>
    </div>

    @forelse($occupancy as $area)
    <div class="col-lg-6 mb-4">
        <div class="card shadow-sm h-100 border-0">
            {{-- AREA HEADER --}}
            <div class="card-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="d-flex justify-content-between align-items-center text-white">
                    <div>
                        <h6 class="mb-0 fw-bold">
                            <i class="fas fa-map-marker-alt me-2"></i>{{ $area['area_name'] }}
                        </h6>
                        <small class="opacity-75">{{ $area['area_lokasi'] }}</small>
                    </div>
                    <div class="text-end">
                        <h4 class="mb-0 fw-bold">{{ $area['overall_rate'] }}%</h4>
                        <small class="opacity-75">Occupancy</small>
                    </div>
                </div>
            </div>

            <div class="card-body">
                {{-- OVERALL PROGRESS BAR --}}
                <div class="mb-4">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="fw-bold text-muted">Overall Occupancy</span>
                        <span class="fw-bold">
                            {{ $area['total_terpakai'] }} / {{ $area['total_slot'] }} slot
                        </span>
                    </div>
                    <div class="progress" style="height: 30px;">
                        <div class="progress-bar 
                            @if($area['overall_rate'] >= 90) bg-danger
                            @elseif($area['overall_rate'] >= 70) bg-warning
                            @elseif($area['overall_rate'] >= 50) bg-info
                            @else bg-success
                            @endif
                        " 
                             role="progressbar" 
                             style="width: {{ $area['overall_rate'] }}%;"
                             aria-valuenow="{{ $area['overall_rate'] }}" 
                             aria-valuemin="0" 
                             aria-valuemax="100">
                            <strong>{{ $area['overall_rate'] }}%</strong>
                        </div>
                    </div>
                </div>

                {{-- BREAKDOWN PER TIPE KENDARAAN --}}
                <div class="breakdown-section">
                    <h6 class="mb-3 text-muted">
                        <i class="fas fa-car me-2"></i>Breakdown Per Tipe Kendaraan
                    </h6>
                    
                    @foreach($area['breakdown'] as $detail)
                    <div class="mb-3 pb-3 border-bottom">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <span class="badge bg-secondary me-2">{{ $detail['kode_tipe'] }}</span>
                                <strong>{{ $detail['tipe'] }}</strong>
                            </div>
                            <div class="text-end">
                                <span class="badge 
                                    @if($detail['rate'] >= 90) bg-danger
                                    @elseif($detail['rate'] >= 70) bg-warning
                                    @elseif($detail['rate'] >= 50) bg-info
                                    @else bg-success
                                    @endif
                                ">
                                    {{ $detail['rate'] }}%
                                </span>
                            </div>
                        </div>

                        {{-- Progress bar per tipe --}}
                        <div class="progress mb-2" style="height: 20px;">
                            <div class="progress-bar 
                                @if($detail['rate'] >= 90) bg-danger
                                @elseif($detail['rate'] >= 70) bg-warning
                                @elseif($detail['rate'] >= 50) bg-info
                                @else bg-success
                                @endif
                            " 
                                 style="width: {{ $detail['rate'] }}%">
                            </div>
                        </div>

                        {{-- Detail slot --}}
                        <div class="row g-2">
                            <div class="col-4">
                                <div class="text-center p-2 rounded" style="background: #f8f9fa;">
                                    <small class="text-muted d-block">Total</small>
                                    <strong class="text-primary">{{ $detail['total'] }}</strong>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="text-center p-2 rounded" style="background: #d4edda;">
                                    <small class="text-muted d-block">Tersedia</small>
                                    <strong class="text-success">{{ $detail['tersedia'] }}</strong>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="text-center p-2 rounded" style="background: #f8d7da;">
                                    <small class="text-muted d-block">Terpakai</small>
                                    <strong class="text-danger">{{ $detail['terpakai'] }}</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                {{-- SUMMARY TABLE --}}
                <div class="summary-box mt-3 p-3 rounded" style="background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);">
                    <div class="row text-center">
                        <div class="col-4">
                            <div class="fw-bold text-primary fs-4">{{ $area['total_slot'] }}</div>
                            <small class="text-muted">Total Slot</small>
                        </div>
                        <div class="col-4 border-start border-end">
                            <div class="fw-bold text-success fs-4">{{ $area['total_tersedia'] }}</div>
                            <small class="text-muted">Tersedia</small>
                        </div>
                        <div class="col-4">
                            <div class="fw-bold text-danger fs-4">{{ $area['total_terpakai'] }}</div>
                            <small class="text-muted">Terpakai</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-md-12">
        <div class="alert alert-warning shadow-sm">
            <i class="fas fa-exclamation-triangle me-2"></i>
            Belum ada data kapasitas area parkir
        </div>
    </div>
    @endforelse
</div>

{{-- CUSTOM CSS FOR OCCUPANCY CARDS --}}
<style>
.breakdown-section {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 10px;
}

.breakdown-section .border-bottom:last-child {
    border-bottom: none !important;
    padding-bottom: 0 !important;
}

.summary-box {
    border: 2px solid #c3cfe2;
}

.card-header {
    border-radius: 15px 15px 0 0 !important;
}

.card {
    border-radius: 15px !important;
    transition: transform 0.3s ease;
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.15) !important;
}

.progress {
    border-radius: 10px;
    background-color: #e9ecef;
}

.progress-bar {
    border-radius: 10px;
    font-weight: bold;
    display: flex;
    align-items: center;
    justify-content: center;
}
</style>


{{-- CHART.JS CDN --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<script>
// ========================================
// CHART: Transaksi Per Hari (Bar Chart - EXPANDED)
// ========================================
const ctxHari = document.getElementById('chartPerHari');
const dataPerHari = @json($transaksiPerHari);

new Chart(ctxHari, {
    type: 'bar',
    data: {
        labels: dataPerHari.map(d => d.tanggal),
        datasets: [{
            label: 'Jumlah Transaksi',
            data: dataPerHari.map(d => d.total),
            backgroundColor: 'rgba(54, 162, 235, 0.6)',
            borderColor: 'rgba(54, 162, 235, 1)',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: true
            },
            tooltip: {
                callbacks: {
                    afterLabel: function(context) {
                        const index = context.dataIndex;
                        const hari = dataPerHari[index].hari;
                        const pendapatan = dataPerHari[index].pendapatan;
                        return hari + '\nPendapatan: Rp ' + pendapatan.toLocaleString('id-ID');
                    }
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1
                }
            }
        }
    }
});

// ========================================
// CHART: Pie Tipe Kendaraan
// ========================================
const ctxTipe = document.getElementById('chartTipeKendaraan');
const dataTipe = @json($chartTipe);

new Chart(ctxTipe, {
    type: 'pie',
    data: {
        labels: dataTipe.map(d => d.label),
        datasets: [{
            data: dataTipe.map(d => d.value),
            backgroundColor: [
                'rgba(255, 99, 132, 0.7)',
                'rgba(54, 162, 235, 0.7)',
                'rgba(255, 206, 86, 0.7)',
                'rgba(75, 192, 192, 0.7)',
                'rgba(153, 102, 255, 0.7)',
                'rgba(255, 159, 64, 0.7)'
            ],
            borderColor: [
                'rgba(255, 99, 132, 1)',
                'rgba(54, 162, 235, 1)',
                'rgba(255, 206, 86, 1)',
                'rgba(75, 192, 192, 1)',
                'rgba(153, 102, 255, 1)',
                'rgba(255, 159, 64, 1)'
            ],
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                position: 'bottom'
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        const label = context.label || '';
                        const value = context.parsed;
                        const revenue = dataTipe[context.dataIndex].revenue;
                        return [
                            label + ': ' + value + ' transaksi',
                            'Revenue: Rp ' + revenue.toLocaleString('id-ID')
                        ];
                    }
                }
            }
        }
    }
});

// ========================================
// CHART: Doughnut Metode Pembayaran
// ========================================
const ctxMetode = document.getElementById('chartMetodePembayaran');
const dataMetode = @json($chartMetode);

new Chart(ctxMetode, {
    type: 'doughnut',
    data: {
        labels: dataMetode.map(d => d.label),
        datasets: [{
            data: dataMetode.map(d => d.value),
            backgroundColor: [
                'rgba(255, 99, 132, 0.7)',
                'rgba(54, 162, 235, 0.7)',
                'rgba(255, 206, 86, 0.7)',
                'rgba(75, 192, 192, 0.7)',
                'rgba(153, 102, 255, 0.7)'
            ],
            borderColor: [
                'rgba(255, 99, 132, 1)',
                'rgba(54, 162, 235, 1)',
                'rgba(255, 206, 86, 1)',
                'rgba(75, 192, 192, 1)',
                'rgba(153, 102, 255, 1)'
            ],
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                position: 'bottom'
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        const label = context.label || '';
                        const value = context.parsed;
                        const revenue = dataMetode[context.dataIndex].revenue;
                        return [
                            label + ': ' + value + ' transaksi',
                            'Revenue: Rp ' + revenue.toLocaleString('id-ID')
                        ];
                    }
                }
            }
        }
    }
});
</script>

@endsection