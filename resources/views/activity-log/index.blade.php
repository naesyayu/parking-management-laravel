@extends('app')

@section('title', 'Activity Log')

@section('content')
<div class="container-fluid mt-4 px-4">
    <div class="row">
        <div class="col-12">
            
            {{-- HEADER --}}
            <div class="card shadow-lg mb-4 border-0 header-card">
                <div class="card-body text-white py-4">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h2 class="mb-2 fw-bold">
                                <i class="fas fa-history me-2"></i> Activity Log
                            </h2>
                            <p class="mb-0 opacity-90">
                                Riwayat aktivitas pengguna sistem parkir
                            </p>
                        </div>
                        <div class="col-md-4 text-end">
                            <a href="{{ route('activity-log.export', request()->all()) }}" class="btn btn-light btn-lg">
                                <i class="fas fa-download me-2"></i> Export CSV
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            {{-- FILTER CARD --}}
            <div class="card shadow mb-4 border-0">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold text-primary">
                        <i class="fas fa-filter me-2"></i> Filter
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('activity-log.index') }}" method="GET">
                        <div class="row g-3">
                            {{-- Action Filter --}}
                            <div class="col-md-3">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-cog me-1"></i> Action
                                </label>
                                <select name="action" class="form-select">
                                    <option value="all" {{ request('action') == 'all' ? 'selected' : '' }}>Semua Action</option>
                                    @foreach($actions as $key => $label)
                                        <option value="{{ $key }}" {{ request('action') == $key ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            {{-- User Filter --}}
                            <div class="col-md-3">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-user me-1"></i> User
                                </label>
                                <select name="id_user" class="form-select">
                                    <option value="all" {{ request('id_user') == 'all' ? 'selected' : '' }}>Semua User</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id_user }}" {{ request('id_user') == $user->id_user ? 'selected' : '' }}>
                                            {{ $user->username }} ({{ $user->role->nama_role ?? 'N/A' }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            {{-- Start Date --}}
                            <div class="col-md-2">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-calendar-alt me-1"></i> Dari Tanggal
                                </label>
                                <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                            </div>
                            
                            {{-- End Date --}}
                            <div class="col-md-2">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-calendar-check me-1"></i> Sampai Tanggal
                                </label>
                                <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                            </div>
                            
                            {{-- Buttons --}}
                            <div class="col-md-2 d-flex align-items-end">
                                <div class="d-grid gap-2 w-100">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search me-2"></i> Filter
                                    </button>
                                    <a href="{{ route('activity-log.index') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-redo me-2"></i> Reset
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            
            {{-- DATA TABLE --}}
            <div class="card shadow border-0">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold text-primary">
                        <i class="fas fa-list me-2"></i> Data Activity Log
                        <span class="badge bg-primary">{{ $logs->total() }} records</span>
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th width="5%" class="text-center">No</th>
                                    <th width="15%">Tanggal/Waktu</th>
                                    <th width="10%">User</th>
                                    <th width="12%">Action</th>
                                    <th width="35%">Deskripsi</th>
                                    <th width="15%">IP Address</th>
                                    <th width="8%" class="text-center">Detail</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($logs as $index => $log)
                                <tr>
                                    <td class="text-center">{{ $logs->firstItem() + $index }}</td>
                                    <td>
                                        <i class="fas fa-clock text-muted me-1"></i>
                                        {{ $log->created_at->format('d/m/Y H:i:s') }}
                                    </td>
                                    <td>
                                        <i class="fas fa-user text-primary me-1"></i>
                                        <strong>{{ $log->user?->username ?? 'System' }}</strong>
                                        @if($log->user)
                                            <br>
                                            <small class="text-muted">{{ $log->user->role->nama_role ?? '' }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $badges = [
                                                'login' => 'success',
                                                'logout' => 'secondary',
                                                'transaksi_masuk' => 'info',
                                                'transaksi_keluar' => 'warning',
                                                'cetak_struk' => 'primary',
                                                'tambah_kendaraan' => 'success',
                                                'edit_kendaraan' => 'info',
                                                'hapus_kendaraan' => 'danger',
                                                'export_laporan' => 'dark',
                                            ];
                                            $badgeClass = $badges[$log->action] ?? 'secondary';
                                        @endphp
                                        <span class="badge bg-{{ $badgeClass }}">
                                            {{ $actions[$log->action] ?? $log->action }}
                                        </span>
                                    </td>
                                    <td>
                                        <small>{{ $log->description ?? '-' }}</small>
                                        @if($log->id_transaksi)
                                            <br>
                                            <small class="text-muted">
                                                <i class="fas fa-ticket-alt me-1"></i>
                                                Transaksi #{{ $log->id_transaksi }}
                                                @if($log->transaksi && $log->transaksi->kendaraan)
                                                    ({{ $log->transaksi->kendaraan->plat_nomor }})
                                                @endif
                                            </small>
                                        @endif
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            <i class="fas fa-network-wired me-1"></i>
                                            {{ $log->ip_address ?? '-' }}
                                        </small>
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('activity-log.show', $log->id_log) }}" 
                                           class="btn btn-sm btn-outline-primary"
                                           title="Lihat Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                        <h5 class="text-muted">Tidak ada data</h5>
                                        <p class="text-muted">Belum ada activity log yang tercatat</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                
                {{-- Pagination --}}
                @if($logs->hasPages())
                <div class="card-footer bg-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            Menampilkan {{ $logs->firstItem() }} - {{ $logs->lastItem() }} dari {{ $logs->total() }} data
                        </div>
                        <div>
                            {{ $logs->links() }}
                        </div>
                    </div>
                </div>
                @endif
            </div>
            
        </div>
    </div>
</div>

<style>
.header-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 20px;
}

.card {
    border-radius: 15px;
    transition: all 0.3s ease;
}

.card:hover {
    transform: translateY(-2px);
}

.table thead th {
    border-bottom: 2px solid #667eea;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.85rem;
    letter-spacing: 0.5px;
}

.table tbody tr {
    transition: all 0.2s ease;
}

.table tbody tr:hover {
    background-color: #f8f9fa;
}

.btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
}

.badge {
    padding: 5px 10px;
    font-size: 0.75rem;
}
</style>
@endsection