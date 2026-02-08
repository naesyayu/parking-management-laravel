@extends('app')

@section('title', 'Dashboard Laporan')

@section('content')
<div class="container-fluid">
    
    {{-- HEADER --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-lg border-0 header-card">
                <div class="card-body text-white py-4">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h2 class="mb-2 fw-bold">
                                <i class="fas fa-chart-line me-2"></i> Dashboard Laporan
                            </h2>
                            <p class="mb-0 opacity-90">
                                Selamat datang, <strong>{{ Auth::user()->username }}</strong> ({{ $role->role_user }})
                            </p>
                        </div>
                        <div class="col-md-4 text-end">
                            <div class="clock-card">
                                <div class="clock-time" id="jamSekarang">--:--:--</div>
                                <div class="clock-date" id="tanggalSekarang">Loading...</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ALERT OCCUPANCY (NEW) --}}
    @php
        $alertAreas = $data['occupancy']->filter(function($occ) {
            return $occ['alert_level'] === 'warning' || $occ['alert_level'] === 'full';
        });
    @endphp

    @if($alertAreas->count() > 0)
    <div class="row mb-4">
        <div class="col-12">
            @foreach($alertAreas as $occ)
                @if($occ['alert_level'] === 'full')
                    <div class="alert alert-danger alert-dismissible fade show">
                        <i class="fas fa-ban me-2"></i>
                        <strong>PENUH!</strong> 
                        Area {{ $occ['area'] }} - {{ $occ['tipe'] }} sudah penuh ({{ $occ['persentase'] }}%). 
                        Tidak dapat menerima kendaraan baru.
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @elseif($occ['alert_level'] === 'warning')
                    <div class="alert alert-warning alert-dismissible fade show">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>PERINGATAN!</strong> 
                        Area {{ $occ['area'] }} - {{ $occ['tipe'] }} hampir penuh ({{ $occ['persentase'] }}%). 
                        Segera siapkan area alternatif.
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
            @endforeach
        </div>
    </div>
    @endif
    
    {{-- STATISTIK CARDS --}}
    <div class="row g-4 mb-4">
        {{-- Cards tetap sama ... --}}
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card stat-card-1">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Masuk Hari Ini</h6>
                            <h2 class="mb-0 fw-bold">{{ number_format($data['stats']['transaksi_masuk_hari_ini']) }}</h2>
                            <small class="text-muted">Kendaraan</small>
                        </div>
                        <div class="stat-icon stat-icon-1">
                            <i class="fas fa-sign-in-alt"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card stat-card-2">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Keluar Hari Ini</h6>
                            <h2 class="mb-0 fw-bold">{{ number_format($data['stats']['transaksi_keluar_hari_ini']) }}</h2>
                            <small class="text-muted">Kendaraan</small>
                        </div>
                        <div class="stat-icon stat-icon-2">
                            <i class="fas fa-sign-out-alt"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card stat-card-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Sedang Parkir</h6>
                            <h2 class="mb-0 fw-bold">{{ number_format($data['stats']['kendaraan_parkir_sekarang']) }}</h2>
                            <small class="text-muted">Kendaraan</small>
                        </div>
                        <div class="stat-icon stat-icon-3">
                            <i class="fas fa-parking"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card stat-card-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Pendapatan Hari Ini</h6>
                            <h2 class="mb-0 fw-bold">Rp {{ number_format($data['stats']['pendapatan_hari_ini'], 0, ',', '.') }}</h2>
                            <small class="text-success">
                                <i class="fas fa-tag me-1"></i>
                                Diskon: Rp {{ number_format($data['stats']['diskon_hari_ini'], 0, ',', '.') }}
                            </small>
                        </div>
                        <div class="stat-icon stat-icon-4">
                            <i class="fas fa-money-bill-wave"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- REST OF CONTENT SAMA ... (Charts, Occupancy, Transaksi) --}}
    <div class="row g-4 mb-4">
        <div class="col-lg-8">
            <div class="card shadow border-0">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold text-primary">
                        <i class="fas fa-chart-line me-2"></i> Transaksi 7 Hari Terakhir
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="chartTransaksi"></canvas>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card shadow border-0">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold text-primary">
                        <i class="fas fa-car me-2"></i> Per Tipe Kendaraan
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="chartTipe"></canvas>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row g-4 mb-4">
        <div class="col-lg-6">
            <div class="card shadow border-0">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold text-primary">
                        <i class="fas fa-warehouse me-2"></i> Status Occupancy
                    </h5>
                </div>
                <div class="card-body">
                    @forelse($data['occupancy'] as $occ)
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="fw-bold">{{ $occ['area'] }} - {{ $occ['tipe'] }}</span>
                            <span class="text-muted">
                                {{ $occ['terpakai'] }}/{{ $occ['total'] }} 
                                ({{ $occ['persentase'] }}%)
                            </span>
                        </div>
                        <div class="progress" style="height: 20px;">
                            <div class="progress-bar {{ $occ['alert_level'] === 'full' ? 'bg-danger' : ($occ['alert_level'] === 'warning' ? 'bg-warning' : 'bg-success') }}" 
                                 style="width: {{ $occ['persentase'] }}%">
                                {{ $occ['tersedia'] }} tersedia
                            </div>
                        </div>
                        {{-- Alert Badge --}}
                        @if($occ['alert_level'] === 'full')
                            <small class="badge bg-danger mt-1">PENUH</small>
                        @elseif($occ['alert_level'] === 'warning')
                            <small class="badge bg-warning mt-1">HAMPIR PENUH</small>
                        @endif
                    </div>
                    @empty
                    <p class="text-muted text-center">Tidak ada data occupancy</p>
                    @endforelse
                </div>
            </div>
        </div>
        
        <div class="col-lg-6">
            <div class="card shadow border-0">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold text-primary">
                        <i class="fas fa-credit-card me-2"></i> Metode Pembayaran Hari Ini
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Metode</th>
                                    <th class="text-center">Jumlah</th>
                                    <th class="text-end">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($data['breakdown_metode'] as $metode)
                                <tr>
                                    <td><i class="fas fa-credit-card me-2"></i> {{ $metode['metode'] }}</td>
                                    <td class="text-center">{{ $metode['count'] }}</td>
                                    <td class="text-end fw-bold">Rp {{ number_format($metode['total'], 0, ',', '.') }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted">Belum ada transaksi</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-12">
            <div class="card shadow border-0">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold text-primary">
                        <i class="fas fa-list me-2"></i> Kendaraan Sedang Parkir
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>No</th>
                                    <th>Kode Tiket</th>
                                    <th>Plat Nomor</th>
                                    <th>Tipe</th>
                                    <th>Area</th>
                                    <th>Waktu Masuk</th>
                                    <th>Durasi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($data['transaksi_terbaru'] as $index => $trx)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td><code>{{ $trx->kode_tiket }}</code></td>
                                    <td><strong>{{ $trx->kendaraan->plat_nomor }}</strong></td>
                                    <td>{{ $trx->kendaraan->tipe->tipe_kendaraan ?? '-' }}</td>
                                    <td>{{ $trx->areaParkir->lokasi ?? '-' }}</td>
                                    <td>{{ $trx->waktu_masuk->format('d/m/Y H:i') }}</td>
                                    <td>{{ $trx->waktu_masuk->diffForHumans() }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <i class="fas fa-inbox fa-2x text-muted mb-2"></i>
                                        <p class="text-muted mb-0">Tidak ada kendaraan yang sedang parkir</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
</div>

@push('scripts')
<!-- SweetAlert2 CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<script>
// ========================================
// LOGIN SUCCESS NOTIFICATION (TOAST)
// ========================================
@if(session('login_success'))
    const username = "{{ session('username') ?? Auth::user()->username }}";
    
    Swal.fire({
        toast: true,
        position: 'top-end',
        icon: 'success',
        title: `${username} berhasil login!`,
        text: `Selamat datang ${username}!`,
        showConfirmButton: false,
        timer: 4000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer)
            toast.addEventListener('mouseleave', Swal.resumeTimer)
        }
    });
    
    // OPTIONAL: Play sound
    // const audio = new Audio('/sounds/notification.mp3');
    // audio.play().catch(e => console.log('Audio play failed:', e));
@endif

// ========================================
// Real-time Clock
// ========================================
function updateJam() {
    var now = new Date();
    var jam = String(now.getHours()).padStart(2, '0');
    var menit = String(now.getMinutes()).padStart(2, '0');
    var detik = String(now.getSeconds()).padStart(2, '0');
    
    var hari = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
    var bulan = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agt', 'Sep', 'Okt', 'Nov', 'Des'];
    
    document.getElementById('jamSekarang').textContent = jam + ':' + menit + ':' + detik;
    document.getElementById('tanggalSekarang').textContent = 
        hari[now.getDay()] + ', ' + now.getDate() + ' ' + bulan[now.getMonth()] + ' ' + now.getFullYear();
}

setInterval(updateJam, 1000);
updateJam();

// ========================================
// Chart Transaksi 7 Hari
// ========================================
const ctxTransaksi = document.getElementById('chartTransaksi').getContext('2d');
new Chart(ctxTransaksi, {
    type: 'line',
    data: {
        labels: @json($data['chart_labels']),
        datasets: [{
            label: 'Transaksi',
            data: @json($data['chart_data_transaksi']),
            borderColor: 'rgb(102, 126, 234)',
            backgroundColor: 'rgba(102, 126, 234, 0.1)',
            tension: 0.4,
            fill: true
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                display: true,
                position: 'top'
            }
        },
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});

// ========================================
// Chart Tipe Kendaraan (Doughnut)
// ========================================
const ctxTipe = document.getElementById('chartTipe').getContext('2d');
new Chart(ctxTipe, {
    type: 'doughnut',
    data: {
        labels: @json($data['breakdown_tipe']->pluck('tipe')),
        datasets: [{
            data: @json($data['breakdown_tipe']->pluck('count')),
            backgroundColor: [
                'rgba(102, 126, 234, 0.8)',
                'rgba(118, 75, 162, 0.8)',
                'rgba(250, 112, 154, 0.8)',
                'rgba(254, 234, 64, 0.8)',
                'rgba(56, 239, 125, 0.8)'
            ]
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});
</script>
@endpush

<style>
.header-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 20px;
}

.clock-card {
    background: rgba(255, 255, 255, 0.2);
    backdrop-filter: blur(10px);
    border-radius: 15px;
    padding: 15px;
}

.clock-time {
    font-size: 2rem;
    font-weight: bold;
    font-family: 'Courier New', monospace;
}

.clock-date {
    font-size: 0.9rem;
    opacity: 0.9;
}

.stat-card {
    border-radius: 15px;
    transition: all 0.3s ease;
    border-left: 4px solid;
}

.stat-card-1 { border-left-color: #667eea; }
.stat-card-2 { border-left-color: #764ba2; }
.stat-card-3 { border-left-color: #fa709a; }
.stat-card-4 { border-left-color: #38ef7d; }

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.1);
}

.stat-icon {
    font-size: 2.5rem;
    opacity: 0.3;
}

.stat-icon-1 { color: #667eea; }
.stat-icon-2 { color: #764ba2; }
.stat-icon-3 { color: #fa709a; }
.stat-icon-4 { color: #38ef7d; }

.card {
    border-radius: 15px;
}

.table thead th {
    border-bottom: 2px solid #667eea;
}
</style>
@endsection