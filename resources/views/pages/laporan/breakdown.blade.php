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
                {{-- TOMBOL SWITCH KE DETAIL TRANSAKSI (KHUSUS ADMIN) --}}
                <a href="{{ route('laporan.detail-transaksi') }}" class="btn btn-info me-2">
                    <i class="fas fa-list"></i> Lihat Detail Transaksi
                </a>
                {{-- EXPORT HANYA ADMIN --}}
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
            <form method="GET" action="{{ route('laporan.index') }}" class="row g-2">
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
                    <a href="{{ route('laporan.index') }}" class="btn btn-sm btn-secondary">
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

    {{-- OCCUPANCY RATE PER AREA --}}
    <div class="row mb-4">
        <div class="col-md-12">
            <h5 class="mb-3"><i class="fas fa-parking"></i> Status Slot Parkir Per Area</h5>
        </div>
        @forelse($occupancy as $item)
        <div class="col-md-6 mb-3">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <h6 class="card-title">{{ $item['area'] }}</h6>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <small class="text-muted">Occupancy Rate:</small>
                            <small class="fw-bold">{{ $item['rate'] }}%</small>
                        </div>
                        <div class="progress" style="height: 25px;">
                            <div class="progress-bar {{ $item['rate'] > 80 ? 'bg-danger' : ($item['rate'] > 50 ? 'bg-warning' : 'bg-success') }}" 
                                 role="progressbar" 
                                 style="width: {{ $item['rate'] }}%">
                            </div>
                        </div>
                    </div>
                    <table class="table table-sm mb-0">
                        <tr>
                            <td class="text-muted">Kapasitas Total:</td>
                            <td class="fw-bold">{{ $item['total'] }} slot</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Tersedia:</td>
                            <td class="text-success fw-bold">{{ $item['tersedia'] }} slot</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Terpakai:</td>
                            <td class="text-danger fw-bold">{{ $item['terpakai'] }} slot</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        @empty
        <div class="col-md-12">
            <div class="alert alert-warning">Belum ada data slot kapasitas</div>
        </div>
        @endforelse
    </div>

</div>
@endsection