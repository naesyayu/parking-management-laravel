@extends('app')

@section('title', 'Parkir Keluar')

@section('content')
<div class="container-fluid mt-4 px-4">
    <div class="row justify-content-center">
        <div class="col-12 col-xl-11">

            {{-- HEADER --}}
            <div class="card shadow-lg mb-4 border-0 header-card">
                <div class="card-body text-white py-4">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h2 class="mb-2 fw-bold">
                                <i class="fas fa-sign-out-alt me-2"></i> Parkir Keluar
                            </h2>
                            <p class="mb-0 opacity-90">
                                <i class="fas fa-qrcode me-2"></i> Scan QR Code atau input kode tiket manual
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

            {{-- ALERT ERROR --}}
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show shadow" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i> 
                    <strong>Error!</strong> {{ session('error') }}
                    <button class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="row g-4">
                {{-- KOLOM KIRI --}}
                <div class="col-lg-6">
                    {{-- QR SCANNER --}}
                    <div class="card shadow-lg border-0 mb-4 scanner-card">
                        <div class="card-header text-white py-3">
                            <h5 class="mb-0 fw-bold">
                                <i class="fas fa-camera me-2"></i> Scan QR Code
                            </h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="scanner-wrapper">
                                <div id="qr-reader"></div>
                            </div>
                            <div id="qr-reader-results" class="mt-3"></div>
                            <div class="info-box mt-3">
                                <i class="fas fa-lightbulb"></i>
                                <div>
                                    <strong>Tips:</strong> Arahkan QR Code ke kamera. Pastikan pencahayaan cukup terang.
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- INPUT MANUAL --}}
                    <div class="card shadow-lg border-0 input-card">
                        <div class="card-header text-white py-3">
                            <h5 class="mb-0 fw-bold">
                                <i class="fas fa-keyboard me-2"></i> Input Manual
                            </h5>
                        </div>
                        <div class="card-body p-4">
                            <label class="form-label fw-bold">
                                <i class="fas fa-ticket-alt me-2"></i> Kode Tiket
                            </label>
                            <div class="input-group input-group-lg mb-3">
                                <span class="input-group-text">
                                    <i class="fas fa-barcode"></i>
                                </span>
                                <input 
                                    type="text" 
                                    id="inputKodeTiket" 
                                    class="form-control" 
                                    placeholder="TK20260127..."
                                    autocomplete="off"
                                >
                            </div>
                            <small class="text-muted d-block mb-3">
                                <i class="fas fa-info-circle me-1"></i> Ketik kode tiket lalu tekan <kbd>Enter</kbd>
                            </small>
                            <button type="button" class="btn btn-primary btn-lg w-100" onclick="cekTiketManual()">
                                <i class="fas fa-search me-2"></i> Cek Tiket
                            </button>
                        </div>
                    </div>
                </div>

                {{-- KOLOM KANAN --}}
                <div class="col-lg-6">
                    <div class="card shadow-lg border-0 preview-card">
                        <div class="card-header text-white py-3">
                            <h5 class="mb-0 fw-bold">
                                <i class="fas fa-info-circle me-2"></i> Informasi Parkir
                            </h5>
                        </div>
                        <div class="card-body p-4">
                            <div id="loadingPreview" style="display: none;" class="text-center py-5">
                                <div class="spinner-border text-primary mb-3" style="width: 3rem; height: 3rem;">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <h5 class="text-primary">Memuat Data...</h5>
                            </div>
                            <div id="previewData" style="display: none;"></div>
                            <div id="placeholderPreview" class="text-center py-5">
                                <i class="fas fa-qrcode placeholder-icon"></i>
                                <h5 class="text-muted mt-3">Menunggu Scan...</h5>
                                <p class="text-secondary small">Scan QR Code atau input kode tiket</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- FORM PEMBAYARAN --}}
            <form action="{{ route('parkir.keluar.proses') }}" method="POST" id="formKeluar" style="display: none;">
                @csrf
                <input type="hidden" name="kode_tiket" id="formKodeTiket">
                
                <div class="card shadow-lg border-0 mt-4 payment-card">
                    <div class="card-header text-white py-3">
                        <h5 class="mb-0 fw-bold">
                            <i class="fas fa-credit-card me-2"></i> Pembayaran
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row">
                            <div class="col-md-8 mb-3 mb-md-0">
                                <label class="form-label fw-bold">
                                    Metode Pembayaran <span class="text-danger">*</span>
                                </label>
                                <select name="id_metode" class="form-select form-select-lg" required>
                                    <option value="">-- Pilih Metode --</option>
                                    @foreach($metode as $m)
                                        {{-- PERBAIKAN: Ganti nama_metode jadi metode_bayar --}}
                                        <option value="{{ $m->id_metode }}">{{ $m->metode_bayar }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 d-flex align-items-end">
                                <button type="submit" class="btn btn-success btn-lg w-100" id="btnProses">
                                    <i class="fas fa-check-circle me-2"></i> <span id="btnText">Proses</span>
                                </button>
                            </div>
                        </div>
                        <div class="mt-3">
                            <button type="button" class="btn btn-outline-secondary" onclick="resetForm()">
                                <i class="fas fa-times me-2"></i> Batal
                            </button>
                        </div>
                    </div>
                </div>
            </form>

        </div>
    </div>
</div>

<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>

<style>
.header-card { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 20px; }
.clock-card { background: rgba(255, 255, 255, 0.2); backdrop-filter: blur(10px); border-radius: 15px; padding: 15px; }
.clock-time { font-size: 2rem; font-weight: bold; font-family: 'Courier New', monospace; }
.clock-date { font-size: 0.9rem; opacity: 0.9; }
.scanner-card { border-radius: 20px; }
.scanner-card .card-header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 20px 20px 0 0; }
.scanner-wrapper { background: linear-gradient(135deg, #667eea, #764ba2); padding: 15px; border-radius: 15px; }
#qr-reader { border: 4px solid white; border-radius: 10px; overflow: hidden; }
#qr-reader__dashboard_section_swaplink { display: none !important; }
.input-card { border-radius: 20px; }
.input-card .card-header { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); border-radius: 20px 20px 0 0; }
.preview-card { border-radius: 20px; min-height: 550px; }
.preview-card .card-header { background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); border-radius: 20px 20px 0 0; }
.placeholder-icon { font-size: 6rem; color: #e0e0e0; animation: pulse 2s infinite; }
@keyframes pulse { 0%, 100% { opacity: 1; } 50% { opacity: 0.5; } }
.payment-card { border-radius: 20px; }
.payment-card .card-header { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); border-radius: 20px 20px 0 0; }
.info-box { background: linear-gradient(135deg, #e0f7fa 0%, #b2ebf2 100%); border-radius: 10px; padding: 15px; display: flex; align-items: center; gap: 15px; }
.info-box i { font-size: 2rem; color: #0097a7; }
.btn-primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; border-radius: 10px; }
.btn-success { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); border: none; border-radius: 10px; }
.btn:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(0,0,0,0.2); transition: all 0.3s ease; }
.input-group-text { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none; }
.form-control:focus { border-color: #667eea; box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25); }
kbd { background: #667eea; color: white; padding: 3px 8px; border-radius: 5px; font-size: 0.9rem; }
.card { transition: all 0.3s ease; }
.card:hover { transform: translateY(-5px); }
@media (max-width: 768px) {
    .clock-time { font-size: 1.5rem; }
    .placeholder-icon { font-size: 4rem; }
}
</style>

<script>
(function() {
    'use strict';
    console.log('=== SCRIPT START ===');
    
    var html5QrCode;
    var cekUrl = "{{ route('parkir.keluar.cek') }}";
    var csrfToken = "{{ csrf_token() }}";
    
    function updateJam() {
        var now = new Date();
        var jam = String(now.getHours()).padStart(2, '0');
        var menit = String(now.getMinutes()).padStart(2, '0');
        var detik = String(now.getSeconds()).padStart(2, '0');
        var hari = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        var bulan = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agt', 'Sep', 'Okt', 'Nov', 'Des'];
        document.getElementById('jamSekarang').textContent = jam + ':' + menit + ':' + detik;
        document.getElementById('tanggalSekarang').textContent = hari[now.getDay()] + ', ' + now.getDate() + ' ' + bulan[now.getMonth()] + ' ' + now.getFullYear();
    }
    setInterval(updateJam, 1000);
    updateJam();
    
    document.addEventListener('DOMContentLoaded', function() {
        html5QrCode = new Html5Qrcode("qr-reader");
        var config = { fps: 10, qrbox: { width: 300, height: 300 } };
        html5QrCode.start({ facingMode: "environment" }, config, onScanSuccess, onScanError)
            .catch(function(err) {
                console.error("Camera error:", err);
                document.getElementById('qr-reader').innerHTML = '<div class="alert alert-warning m-3">Kamera tidak dapat diakses. Gunakan input manual.</div>';
            });
    });
    
    function onScanSuccess(decodedText) {
        console.log('QR Scanned:', decodedText);
        html5QrCode.stop();
        document.getElementById('qr-reader-results').innerHTML = '<div class="alert alert-success"><i class="fas fa-check-circle me-2"></i> QR Code: <strong>' + decodedText + '</strong></div>';
        cekTiket(decodedText);
    }
    
    function onScanError(errorMessage) {  }
    
    function cekTiket(kodeTiket) {
        document.getElementById('placeholderPreview').style.display = 'none';
        document.getElementById('loadingPreview').style.display = 'block';
        document.getElementById('previewData').style.display = 'none';
        
        fetch(cekUrl, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify({ kode_tiket: kodeTiket })
        })
        .then(function(response) { return response.json(); })
        .then(function(data) {
            document.getElementById('loadingPreview').style.display = 'none';
            if (data.success) {
                showPreviewData(data.data);
                document.getElementById('formKodeTiket').value = kodeTiket;
                document.getElementById('formKeluar').style.display = 'block';
            } else {
                showError(data.message);
                resetScanner();
            }
        })
        .catch(function(error) {
            console.error('Error:', error);
            document.getElementById('loadingPreview').style.display = 'none';
            showError('Terjadi kesalahan');
            resetScanner();
        });
    }
    
    window.cekTiketManual = function() {
        var kodeTiket = document.getElementById('inputKodeTiket').value.trim();
        if (!kodeTiket) { alert('Masukkan kode tiket!'); return; }
        cekTiket(kodeTiket);
    };
    
    document.getElementById('inputKodeTiket').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') { window.cekTiketManual(); }
    });
    
    function showPreviewData(data) {
        var isMember = data.member !== null;
        var html = '<div class="table-responsive"><table class="table table-bordered">';
        html += '<tr><th class="bg-light" width="40%">Kode Tiket</th><td><strong class="text-primary">' + data.kode_tiket + '</strong></td></tr>';
        html += '<tr><th class="bg-light">Plat Nomor</th><td><strong>' + data.plat_nomor + '</strong></td></tr>';
        html += '<tr><th class="bg-light">Tipe Kendaraan</th><td>' + data.tipe_kendaraan + '</td></tr>';
        if (isMember) {
            html += '<tr><th class="bg-light">Pemilik</th><td>' + data.pemilik + ' <span class="badge bg-success">Member ' + data.member.level + '</span></td></tr>';
        } else {
            html += '<tr><th class="bg-light">Pemilik</th><td>' + data.pemilik + '</td></tr>';
        }
        html += '<tr><th class="bg-light">Area</th><td>' + data.area + '</td></tr>';
        html += '<tr><th class="bg-light">Waktu Masuk</th><td>' + data.waktu_masuk + '</td></tr>';
        html += '<tr><th class="bg-light">Waktu Keluar</th><td>' + data.waktu_keluar + '</td></tr>';
        html += '<tr><th class="bg-light">Durasi</th><td><strong class="text-danger">' + data.durasi_jam + ' jam</strong> (' + data.durasi_menit + ' menit)</td></tr>';
        html += '<tr class="table-info"><th class="bg-light">Total Tarif</th><td><strong>Rp ' + formatRupiah(data.total_tarif) + '</strong></td></tr>';
        if (isMember) {
            html += '<tr class="table-success"><th class="bg-light">Diskon (' + data.persen_diskon + '%)</th><td><strong class="text-success">- Rp ' + formatRupiah(data.diskon) + '</strong></td></tr>';
        }
        html += '<tr class="table-warning"><th class="bg-light"><h5 class="mb-0">TOTAL BAYAR</h5></th><td><h4 class="mb-0 text-danger">Rp ' + formatRupiah(data.total_bayar) + '</h4></td></tr>';
        html += '</table></div>';
        document.getElementById('previewData').innerHTML = html;
        document.getElementById('previewData').style.display = 'block';
    }
    
    function showError(message) {
        document.getElementById('previewData').innerHTML = '<div class="alert alert-danger"><i class="fas fa-exclamation-triangle me-2"></i> ' + message + '</div>';
        document.getElementById('previewData').style.display = 'block';
    }
    
    window.resetForm = function() {
        document.getElementById('formKeluar').style.display = 'none';
        document.getElementById('previewData').style.display = 'none';
        document.getElementById('placeholderPreview').style.display = 'block';
        document.getElementById('inputKodeTiket').value = '';
        document.getElementById('qr-reader-results').innerHTML = '';
        resetScanner();
    };
    
    function resetScanner() {
        var config = { fps: 10, qrbox: { width: 300, height: 300 } };
        html5QrCode.start({ facingMode: "environment" }, config, onScanSuccess, onScanError)
            .catch(function(err) { console.error(err); });
    }
    
    function formatRupiah(angka) {
        return new Intl.NumberFormat('id-ID').format(angka);
    }
    
    document.getElementById('formKeluar').addEventListener('submit', function() {
        document.getElementById('btnProses').disabled = true;
        document.getElementById('btnText').innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Memproses...';
    });
    
    window.addEventListener('beforeunload', function() {
        if (html5QrCode) {
            html5QrCode.stop().catch(function(err) { console.error(err); });
        }
    });
    
    console.log('=== SCRIPT READY ===');
})();
</script>
@endsection