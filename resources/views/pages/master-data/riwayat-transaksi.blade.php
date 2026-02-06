@extends('app')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-gradient-orange-purple">
                    <h4 class="mb-0">
                        <i class="fas fa-history me-2"></i>Riwayat Transaksi Parkir
                    </h4>
                </div>

                <div class="card-body">
                    
                    <!-- FORM FILTER -->
                    <form method="GET" class="mb-4">
                        <div class="row g-3">
                            
                            <!-- Filter Tanggal Dari -->
                            <div class="col-md-3">
                                <label class="form-label">Tanggal Dari</label>
                                <input type="date" 
                                       name="tanggal_dari" 
                                       class="form-control" 
                                       value="{{ request('tanggal_dari') }}">
                            </div>

                            <!-- Filter Tanggal Sampai -->
                            <div class="col-md-3">
                                <label class="form-label">Tanggal Sampai</label>
                                <input type="date" 
                                       name="tanggal_sampai" 
                                       class="form-control" 
                                       value="{{ request('tanggal_sampai') }}">
                            </div>

                            <!-- Filter Plat Nomor -->
                            <div class="col-md-3">
                                <label class="form-label">Plat Nomor</label>
                                <input type="text" 
                                       name="plat_nomor" 
                                       class="form-control" 
                                       placeholder="Cari plat..." 
                                       value="{{ request('plat_nomor') }}">
                            </div>

                            <!-- Filter Area -->
                            <div class="col-md-3">
                                <label class="form-label">Area Parkir</label>
                                <select name="id_area" class="form-select">
                                    <option value="">Semua Area</option>
                                    @foreach($areaParkir as $area)
                                        <option value="{{ $area->id_area }}" 
                                            {{ request('id_area') == $area->id_area ? 'selected' : '' }}>
                                            {{ $area->kode_area }} - {{ $area->lokasi }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Tombol -->
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search me-1"></i>Cari
                                </button>
                                <a href="{{ route('master-data.riwayat-transaksi') }}" 
                                   class="btn btn-secondary">
                                    <i class="fas fa-redo me-1"></i>Reset
                                </a>
                            </div>

                        </div>
                    </form>

                    <!-- INFO TOTAL -->
                    <div class="alert alert-info mb-3">
                        Total: <strong>{{ $transaksi->total() }}</strong> transaksi
                    </div>

                    <!-- TABEL -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>No</th>
                                    <th>Kode Tiket</th>
                                    <th>Plat Nomor</th>
                                    <th>Area</th>
                                    <th>Waktu Masuk</th>
                                    <th>Waktu Keluar</th>
                                    <th>Durasi</th>
                                    <th>Total Bayar</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($transaksi as $index => $tr)
                                <tr>
                                    <td>{{ $transaksi->firstItem() + $index }}</td>
                                    <td>{{ $tr->kode_tiket }}</td>
                                    <td>
                                        <strong>{{ $tr->kendaraan->plat_nomor ?? '-' }}</strong>
                                        <br>
                                        <small class="text-muted">
                                            {{ $tr->kendaraan->tipeKendaraan->tipe_kendaraan ?? '-' }}
                                        </small>
                                    </td>
                                    <td>{{ $tr->areaParkir->lokasi ?? '-' }}</td>
                                    <td>{{ $tr->waktu_masuk->format('d/m/Y H:i') }}</td>
                                    <td>{{ $tr->waktu_keluar ? $tr->waktu_keluar->format('d/m/Y H:i') : '-' }}</td>
                                    <td>{{ $tr->durasi_jam }} Jam</td>
                                    <td><strong>Rp {{ number_format($tr->total_bayar, 0, ',', '.') }}</strong></td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted">
                                        Tidak ada data transaksi
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- PAGINATION -->
                    {{ $transaksi->links() }}

                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-gradient-orange-purple {
        background: linear-gradient(135deg, #ffb347 0%, #ff7a18 45%, #667eea 100%);
        color: white;
    }
</style>

@endsection