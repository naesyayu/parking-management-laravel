@extends('app')

@section('title', 'Tiket Parkir Masuk')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-7">
            
            {{-- HEADER SUCCESS --}}
            <div class="text-center mb-4 no-print">
                <div class="display-1 text-success mb-3">
                    <i class="fas fa-check-circle"></i>
                </div>
                <h2 class="fw-bold">Transaksi Berhasil!</h2>
                <p class="text-muted">Tiket parkir telah berhasil dibuat</p>
            </div>

            {{-- CARD TIKET --}}
            <div class="card shadow-lg border-0" id="tiketCard">
                
                {{-- HEADER --}}
                <div class="card-header text-white text-center py-4" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <h4 class="mb-0 fw-bold">
                        <i class="fas fa-ticket-alt"></i> TIKET PARKIR MASUK
                    </h4>
                    <small class="opacity-75">Simpan tiket ini untuk keluar parkir</small>
                </div>

                <div class="card-body p-4">
                    
                    {{-- QR CODE --}}
                    <div class="text-center mb-4 p-3 bg-light rounded">
                        <img 
                            src="data:image/png;base64,{{ $qr }}" 
                            alt="QR Code" 
                            class="img-fluid shadow-sm" 
                            style="max-width: 280px; border-radius: 8px;"
                        >
                        <p class="text-muted small mt-2 mb-0">
                            <i class="fas fa-qrcode"></i> Scan QR saat keluar
                        </p>
                    </div>

                    {{-- INFO TIKET --}}
                    <div class="border rounded p-3 mb-3">
                        <table class="table table-borderless mb-0">
                            <tr>
                                <td class="fw-bold text-muted" width="45%">
                                    <i class="fas fa-barcode text-primary"></i> Kode Tiket
                                </td>
                                <td class="text-end">
                                    <span class="badge bg-primary fs-6 px-3 py-2">
                                        {{ $transaksi->kode_tiket }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-muted">
                                    <i class="fas fa-id-card text-success"></i> Plat Nomor
                                </td>
                                <td class="text-end">
                                    <strong class="fs-5 text-success">{{ $transaksi->kendaraan->plat_nomor }}</strong>
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-muted">
                                    <i class="fas fa-motorcycle text-info"></i> Tipe Kendaraan
                                </td>
                                <td class="text-end">
                                    <span class="badge bg-info px-3 py-2">
                                        {{ $transaksi->kendaraan->tipe->tipe_kendaraan }}
                                    </span>
                                </td>
                            </tr>
                        </table>
                    </div>

                    {{-- DETAIL PARKIR --}}
                    <div class="p-3 bg-light rounded">
                        <h6 class="fw-bold mb-3">
                            <i class="fas fa-info-circle text-warning"></i> Detail Parkir
                        </h6>
                        <table class="table table-sm table-borderless mb-0">
                            <tr>
                                <td class="text-muted" width="45%">
                                    <i class="fas fa-clock"></i> Waktu Masuk
                                </td>
                                <td class="text-end fw-bold">
                                    {{ $transaksi->waktu_masuk->format('d/m/Y H:i:s') }}
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted">
                                    <i class="fas fa-map-marker-alt"></i> Area Parkir
                                </td>
                                <td class="text-end fw-bold">
                                    {{ $transaksi->areaParkir->lokasi }}
                                </td>
                            </tr>
                            @if(isset($transaksi->user) && $transaksi->user)
                            <tr>
                                <td class="text-muted">
                                    <i class="fas fa-user"></i> Petugas
                                </td>
                                <td class="text-end">
                                    {{ $transaksi->user->name }}
                                </td>
                            </tr>
                            @endif
                        </table>
                    </div>

                    {{-- DURASI BERJALAN --}}
                    <div class="mt-3 p-3 border border-warning rounded bg-warning bg-opacity-10">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fw-bold">
                                <i class="fas fa-hourglass-half text-warning"></i> Durasi Parkir
                            </span>
                            <span class="fs-4 fw-bold text-warning" id="durasiParkir">
                                00:00:00
                            </span>
                        </div>
                        <small class="text-muted d-block mt-2">
                            <i class="fas fa-info-circle"></i> Timer otomatis sejak masuk
                        </small>
                    </div>

                    {{-- ALERT --}}
                    <div class="alert alert-info border-0 mt-3 shadow-sm no-print">
                        <h6 class="alert-heading">
                            <i class="fas fa-exclamation-circle"></i> Penting!
                        </h6>
                        <ul class="mb-0 small">
                            <li>Simpan tiket ini dengan baik</li>
                            <li>Tunjukkan QR Code saat keluar</li>
                            <li>Tiket hilang dikenakan biaya tambahan</li>
                        </ul>
                    </div>

                    {{-- TOMBOL --}}
                    <div class="d-grid gap-2 mt-4 no-print">
                        <button onclick="cetakTiket()" class="btn btn-primary btn-lg">
                            <i class="fas fa-print"></i> Cetak Tiket
                        </button>
                        <a href="{{ route('parkir.masuk') }}" class="btn btn-success btn-lg">
                            <i class="fas fa-plus-circle"></i> Transaksi Baru
                        </a>
                    </div>
                </div>

                {{-- FOOTER --}}
                <div class="card-footer text-center text-muted py-3 bg-light">
                    <small>
                        <i class="fas fa-shield-alt"></i> Tiket otomatis dari sistem<br>
                        Dicetak: <strong>{{ now()->format('d/m/Y H:i:s') }}</strong>
                    </small>
                </div>
            </div>

        </div>
    </div>
</div>

<style>
@media print {
    body * {
        visibility: hidden;
    }
    
    #tiketCard,
    #tiketCard * {
        visibility: visible;
    }
    
    #tiketCard {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        box-shadow: none !important;
    }
    
    .no-print {
        display: none !important;
    }
    
    .card-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }
}

.card {
    animation: slideUp 0.5s ease-out;
}

@keyframes slideUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>

<script>
// TIMER DURASI PARKIR
var waktuMasuk = new Date('{{ $transaksi->waktu_masuk->format('Y-m-d H:i:s') }}');

function updateDurasi() {
    var sekarang = new Date();
    var selisih = Math.floor((sekarang - waktuMasuk) / 1000);
    
    var jam = Math.floor(selisih / 3600);
    var menit = Math.floor((selisih % 3600) / 60);
    var detik = selisih % 60;
    
    var durasiText = 
        String(jam).padStart(2, '0') + ':' +
        String(menit).padStart(2, '0') + ':' +
        String(detik).padStart(2, '0');
    
    var elem = document.getElementById('durasiParkir');
    if (elem) {
        elem.textContent = durasiText;
    }
}

setInterval(updateDurasi, 1000);
updateDurasi();

// FUNGSI CETAK
function cetakTiket() {
    window.print();
}

console.log('Tiket loaded successfully');
</script>
@endsection