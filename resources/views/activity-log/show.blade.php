@extends('app')

@section('title', 'Detail Activity Log')

@section('content')
<div class="container-fluid mt-4 px-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            
            {{-- BACK BUTTON --}}
            <div class="mb-3">
                <a href="{{ route('activity-log.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i> Kembali
                </a>
            </div>
            
            {{-- MAIN CARD --}}
            <div class="card shadow-lg border-0">
                <div class="card-header text-white py-4 detail-header">
                    <h3 class="mb-0 fw-bold">
                        <i class="fas fa-file-alt me-2"></i> Detail Activity Log
                    </h3>
                    <small class="opacity-75">ID: #{{ $log->id_log }}</small>
                </div>
                
                <div class="card-body p-4">
                    
                    {{-- BASIC INFO --}}
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="info-box">
                                <label class="info-label">
                                    <i class="fas fa-calendar-alt me-2"></i> Tanggal/Waktu
                                </label>
                                <div class="info-value">
                                    {{ $log->created_at->format('d F Y, H:i:s') }}
                                    <small class="text-muted">({{ $log->created_at->diffForHumans() }})</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="info-box">
                                <label class="info-label">
                                    <i class="fas fa-user me-2"></i> User
                                </label>
                                <div class="info-value">
                                    @if($log->user)
                                        <strong>{{ $log->user->username }}</strong>
                                        <span class="badge bg-primary ms-2">{{ $log->user->role->nama_role ?? 'N/A' }}</span>
                                    @else
                                        <em class="text-muted">System</em>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    {{-- ACTION & DESCRIPTION --}}
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="info-box">
                                <label class="info-label">
                                    <i class="fas fa-cog me-2"></i> Action
                                </label>
                                <div class="info-value">
                                    @php
                                        $badges = [
                                            'login' => 'success',
                                            'logout' => 'secondary',
                                            'transaksi_masuk' => 'info',
                                            'transaksi_keluar' => 'warning',
                                            'cetak_struk' => 'primary',
                                            'export_laporan' => 'dark',
                                        ];
                                        $badgeClass = $badges[$log->action] ?? 'secondary';
                                    @endphp
                                    <span class="badge bg-{{ $badgeClass }} badge-lg">{{ $log->action }}</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="info-box">
                                <label class="info-label">
                                    <i class="fas fa-network-wired me-2"></i> IP Address
                                </label>
                                <div class="info-value">
                                    <code>{{ $log->ip_address ?? '-' }}</code>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    {{-- DESCRIPTION --}}
                    <div class="info-box mb-4">
                        <label class="info-label">
                            <i class="fas fa-align-left me-2"></i> Deskripsi
                        </label>
                        <div class="info-value">
                            {{ $log->description ?? '-' }}
                        </div>
                    </div>
                    
                    {{-- USER AGENT --}}
                    @if($log->user_agent)
                    <div class="info-box mb-4">
                        <label class="info-label">
                            <i class="fas fa-desktop me-2"></i> User Agent
                        </label>
                        <div class="info-value">
                            <small class="text-muted">{{ $log->user_agent }}</small>
                        </div>
                    </div>
                    @endif
                    
                    {{-- TRANSAKSI INFO --}}
                    @if($log->transaksi)
                    <div class="card bg-light mb-4">
                        <div class="card-header bg-gradient-primary text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-ticket-alt me-2"></i> Informasi Transaksi
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <strong>ID Transaksi:</strong><br>
                                    #{{ $log->transaksi->id_transaksi }}
                                </div>
                                <div class="col-md-4">
                                    <strong>Kode Tiket:</strong><br>
                                    {{ $log->transaksi->kode_tiket }}
                                </div>
                                <div class="col-md-4">
                                    <strong>Status:</strong><br>
                                    <span class="badge bg-{{ $log->transaksi->status == 'in' ? 'info' : 'success' }}">
                                        {{ strtoupper($log->transaksi->status) }}
                                    </span>
                                </div>
                            </div>
                            
                            @if($log->transaksi->kendaraan)
                            <hr>
                            <div class="row">
                                <div class="col-md-4">
                                    <strong>Plat Nomor:</strong><br>
                                    {{ $log->transaksi->kendaraan->plat_nomor }}
                                </div>
                                <div class="col-md-4">
                                    <strong>Tipe:</strong><br>
                                    {{ $log->transaksi->kendaraan->tipe->tipe_kendaraan ?? '-' }}
                                </div>
                                <div class="col-md-4">
                                    <strong>Pemilik:</strong><br>
                                    {{ $log->transaksi->kendaraan->pemilik->nama ?? '-' }}
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endif
                    
                    {{-- METADATA --}}
                    @if($log->metadata && count($log->metadata) > 0)
                    <div class="card bg-light">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-database me-2"></i> Metadata
                            </h5>
                        </div>
                        <div class="card-body">
                            <pre class="mb-0"><code>{{ json_encode($log->metadata, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</code></pre>
                        </div>
                    </div>
                    @endif
                    
                </div>
                
                <div class="card-footer bg-white py-3">
                    <div class="text-end">
                        <a href="{{ route('activity-log.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i> Kembali ke List
                        </a>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</div>

<style>
.detail-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.info-box {
    padding: 15px;
    border-left: 4px solid #667eea;
    background: #f8f9fa;
    border-radius: 8px;
    margin-bottom: 15px;
}

.info-label {
    font-weight: 600;
    color: #667eea;
    display: block;
    margin-bottom: 8px;
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.info-value {
    font-size: 1.1rem;
    color: #333;
}

.badge-lg {
    padding: 8px 16px;
    font-size: 1rem;
}

code {
    background: #2d3748;
    color: #48bb78;
    padding: 2px 6px;
    border-radius: 4px;
    font-family: 'Courier New', monospace;
}

pre {
    background: #2d3748;
    color: #e2e8f0;
    padding: 20px;
    border-radius: 8px;
    overflow-x: auto;
}

pre code {
    background: transparent;
    padding: 0;
}

.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}
</style>
@endsection