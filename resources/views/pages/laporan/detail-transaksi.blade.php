@extends('app')

@section('content')
<div class="container-fluid">
    {{-- HEADER --}}
    <div class="row mb-4">
        <div class="col-md-8">
            <h2 class="mb-0">📋 Detail Transaksi</h2>
            @if($role->isPetugas())
                <small class="text-muted">Menampilkan transaksi yang Anda proses</small>
            @else
                <small class="text-muted">Detail lengkap semua transaksi parkir</small>
            @endif
        </div>
        <div class="col-md-4 text-end">
            @if($role->isAdmin())
                {{-- TOMBOL SWITCH KE BREAKDOWN (KHUSUS ADMIN) --}}
                <a href="{{ route('laporan.index') }}" class="btn btn-primary">
                    <i class="fas fa-chart-bar"></i> Lihat Breakdown Laporan
                </a>
            @endif
        </div>
    </div>

    {{-- FILTER FORM --}}
    <div class="card mb-3 shadow-sm">
        <div class="card-header bg-light">
            <h5 class="mb-0"><i class="fas fa-filter"></i> Filter & Pencarian</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('laporan.detail-transaksi') }}" class="row g-2">
                
                {{-- Plat Nomor --}}
                <div class="col-md-3">
                    <label class="form-label small text-muted">Plat Nomor</label>
                    <input type="text" name="plat_nomor" class="form-control form-control-sm" 
                           placeholder="Cth: D 1234 AB" value="{{ request('plat_nomor') }}">
                </div>
                
                {{-- Period Type --}}
                <div class="col-md-3">
                    <label class="form-label small text-muted">Periode</label>
                    <select name="period_type" class="form-select form-select-sm" id="periodType">
                        <option value="custom" {{ request('period_type') === 'custom' || !request('period_type') ? 'selected' : '' }}>Custom</option>
                        <option value="today" {{ request('period_type') === 'today' ? 'selected' : '' }}>Hari Ini</option>
                        <option value="week" {{ request('period_type') === 'week' ? 'selected' : '' }}>Minggu Ini</option>
                        <option value="month" {{ request('period_type') === 'month' ? 'selected' : '' }}>Bulan Ini</option>
                    </select>
                </div>

                {{-- Tipe Kendaraan --}}
                <div class="col-md-3">
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

                {{-- Date Range --}}
                <div class="col-md-2" id="dateFrom">
                    <label class="form-label small text-muted">Dari Tanggal</label>
                    <input type="date" name="start_date" class="form-control form-control-sm" value="{{ request('start_date') }}">
                </div>
                <div class="col-md-2" id="dateTo">
                    <label class="form-label small text-muted">Hingga Tanggal</label>
                    <input type="date" name="end_date" class="form-control form-control-sm" value="{{ request('end_date') }}">
                </div>

                {{-- Buttons --}}
                <div class="col-md-12 pt-3">
                    <button type="submit" class="btn btn-sm btn-primary">
                        <i class="fas fa-search"></i> Cari
                    </button>
                    <a href="{{ route('laporan.detail-transaksi') }}" class="btn btn-sm btn-secondary">
                        <i class="fas fa-redo"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- DATA TABLE --}}
    <div class="card shadow-sm">
        <div class="card-header bg-light">
            <h5 class="mb-0"><i class="fas fa-list"></i> Daftar Transaksi</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Plat Nomor</th>
                            <th>Tipe</th>
                            <th>Area</th>
                            <th>Jam Masuk</th>
                            <th>Jam Keluar</th>
                            <th class="text-center">Durasi</th>
                            <th class="text-end">Tarif</th>
                            <th class="text-end">Diskon</th>
                            <th class="text-end">Total</th>
                            <th>Metode</th>
                            <th>Petugas</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transaksi as $item)
                        <tr>
                            <td><strong>{{ $item->kendaraan->plat_nomor ?? '-' }}</strong></td>
                            <td><small class="text-muted">{{ $item->kendaraan->tipe->tipe_kendaraan ?? '-' }}</small></td>
                            <td>{{ $item->areaParkir->lokasi ?? '-' }}</td>
                            <td><small>{{ $item->waktu_masuk->format('d/m/Y H:i') }}</small></td>
                            <td><small>{{ $item->waktu_keluar->format('d/m/Y H:i') }}</small></td>
                            <td class="text-center">
                                <small>
                                    @if($item->durasi_jam)
                                        {{ number_format($item->durasi_jam, 1) }} jam
                                    @else
                                        -
                                    @endif
                                </small>
                            </td>
                            <td class="text-end">Rp {{ number_format($item->tarifParkir->tarif ?? 0, 0, ',', '.') }}</td>
                            <td class="text-end text-danger">
                                @if($item->diskon > 0)
                                    -Rp {{ number_format($item->diskon, 0, ',', '.') }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="text-end fw-bold">Rp {{ number_format($item->total_bayar, 0, ',', '.') }}</td>
                            <td><small>{{ $item->metodePembayaran->metode_bayar ?? '-' }}</small></td>
                            <td><small>{{ $item->user->username ?? 'N/A' }}</small></td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="11" class="text-center text-muted py-4">
                                Tidak ada data transaksi
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- PAGINATION --}}
    <div class="mt-3">
        {{ $transaksi->appends(request()->query())->links() }}
    </div>
</div>

<script>
// Toggle date inputs based on period type
document.getElementById('periodType').addEventListener('change', function() {
    const dateFrom = document.getElementById('dateFrom');
    const dateTo = document.getElementById('dateTo');
    
    if (this.value === 'custom') {
        dateFrom.style.display = 'block';
        dateTo.style.display = 'block';
    } else {
        dateFrom.style.display = 'none';
        dateTo.style.display = 'none';
    }
});

// Trigger on page load
document.getElementById('periodType').dispatchEvent(new Event('change'));
</script>
@endsection