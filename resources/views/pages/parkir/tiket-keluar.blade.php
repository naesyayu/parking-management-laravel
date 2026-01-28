@extends('app')

@section('title', 'Struk Parkir')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            
            {{-- SUCCESS HEADER --}}
            <div class="text-center mb-4 no-print success-header">
                <div class="success-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <h2 class="fw-bold text-success mt-3">Pembayaran Berhasil!</h2>
                <p class="text-muted">Terima kasih telah menggunakan layanan parkir kami</p>
            </div>

            {{-- CARD STRUK --}}
            <div class="card shadow-lg border-0 struk-card" id="strukCard">
                
                {{-- HEADER --}}
                <div class="card-header text-white text-center py-4 struk-header">
                    <h3 class="mb-0 fw-bold">
                        <i class="fas fa-receipt me-2"></i> STRUK PEMBAYARAN PARKIR
                    </h3>
                    <small class="opacity-90">{{ config('app.name', 'Sistem Parkir') }}</small>
                </div>

                <div class="card-body p-4">
                    
                    {{-- INFO PERUSAHAAN --}}
                    <div class="text-center mb-4 pb-3 border-bottom company-info">
                        <h5 class="fw-bold mb-1">SISTEM PARKIR</h5>
                        <p class="mb-0 small">Jl. Raya Parkir No. 123, Kota ABC</p>
                        <p class="mb-0 small">Telp: (021) 1234-5678</p>
                        <p class="mb-0 small text-muted">{{ now()->format('d/m/Y H:i:s') }}</p>
                    </div>

                    {{-- INFO KENDARAAN --}}
                    <div class="mb-4">
                        <h6 class="fw-bold border-bottom pb-2 section-title">
                            <i class="fas fa-car me-2"></i> Informasi Kendaraan
                        </h6>
                        <table class="table table-sm table-borderless info-table">
                            <tr>
                                <td width="45%">Kode Tiket</td>
                                <td>: <strong class="text-primary">{{ $transaksi->kode_tiket }}</strong></td>
                            </tr>
                            <tr>
                                <td>Plat Nomor</td>
                                <td>: <strong>{{ $transaksi->kendaraan->plat_nomor }}</strong></td>
                            </tr>
                            <tr>
                                <td>Tipe Kendaraan</td>
                                <td>: {{ $transaksi->kendaraan->tipe->tipe_kendaraan }}</td>
                            </tr>
                            <tr>
                                <td>Pemilik</td>
                                <td>: {{ $transaksi->kendaraan->pemilik->nama ?? 'Tidak Diketahui' }}</td>
                            </tr>
                            <tr>
                                <td>Area Parkir</td>
                                <td>: {{ $transaksi->areaParkir->lokasi }}</td>
                            </tr>
                        </table>
                    </div>

                    {{-- INFO WAKTU --}}
                    <div class="mb-4">
                        <h6 class="fw-bold border-bottom pb-2 section-title">
                            <i class="fas fa-clock me-2"></i> Informasi Waktu
                        </h6>
                        <table class="table table-sm table-borderless info-table">
                            <tr>
                                <td width="45%">Waktu Masuk</td>
                                <td>: {{ $transaksi->waktu_masuk->format('d/m/Y H:i:s') }}</td>
                            </tr>
                            <tr>
                                <td>Waktu Keluar</td>
                                <td>: {{ $transaksi->waktu_keluar->format('d/m/Y H:i:s') }}</td>
                            </tr>
                            <tr>
                                <td>Durasi Parkir</td>
                                <td>: <strong class="text-danger">{{ $durasi_jam }} jam</strong> ({{ $durasi_menit }} menit)</td>
                            </tr>
                        </table>
                    </div>

                    {{-- RINCIAN PEMBAYARAN --}}
                    <div class="mb-4">
                        <h6 class="fw-bold border-bottom pb-2 section-title">
                            <i class="fas fa-money-bill-wave me-2"></i> Rincian Pembayaran
                        </h6>
                        <table class="table table-sm payment-table">
                            <tr>
                                <td width="60%">Tarif Parkir ({{ $detailParkir->jam_min }} - {{ $detailParkir->jam_max }} jam)</td>
                                <td class="text-end fw-bold">Rp {{ number_format($total_tarif, 0, ',', '.') }}</td>
                            </tr>
                            
                            @if($member)
                            <tr class="table-success discount-row">
                                <td>
                                    <strong>Diskon Member</strong>
                                    <br><small class="text-muted">
                                        {{ $member->level->nama_level ?? 'Member' }} ({{ $persen_diskon }}%)
                                        <br>Berlaku s.d: {{ Carbon\Carbon::parse($member->berlaku_hingga)->format('d/m/Y') }}
                                    </small>
                                </td>
                                <td class="text-end fw-bold text-success">
                                    - Rp {{ number_format($diskon, 0, ',', '.') }}
                                </td>
                            </tr>
                            @endif
                            
                            <tr class="table-warning total-row">
                                <td><h5 class="mb-0">TOTAL BAYAR</h5></td>
                                <td class="text-end"><h4 class="mb-0 text-danger fw-bold">Rp {{ number_format($total_bayar, 0, ',', '.') }}</h4></td>
                            </tr>
                        </table>
                    </div>

                    {{-- INFO LAINNYA --}}
                    <div class="mb-4 info-box">
                        <h6 class="fw-bold mb-3">
                            <i class="fas fa-info-circle me-2"></i> Informasi Lainnya
                        </h6>
                        <table class="table table-sm table-borderless info-table">
                            <tr>
                                <td width="45%">Metode Pembayaran</td>
                                {{-- PERBAIKAN: Ganti nama_metode jadi metode_bayar --}}
                                <td>: <strong>{{ $transaksi->metodePembayaran->metode_bayar ?? 'N/A' }}</strong></td>
                            </tr>
                            <tr>
                                <td>Petugas</td>
                                <td>: {{ $transaksi->user->name ?? 'Sistem' }}</td>
                            </tr>
                            <tr>
                                <td>Status</td>
                                <td>: <span class="badge bg-success">Lunas</span></td>
                            </tr>
                        </table>
                    </div>

                    {{-- FOOTER --}}
                    <div class="text-center border-top pt-4 mt-4 footer-info">
                        <p class="mb-1 fw-bold">Terima Kasih</p>
                        <p class="mb-0 small text-muted">Simpan struk ini sebagai bukti pembayaran</p>
                        <p class="mb-0 small text-muted">ID Transaksi: {{ $transaksi->id_transaksi }}</p>
                    </div>

                    {{-- TOMBOL AKSI --}}
                    <div class="d-grid gap-2 mt-4 no-print">
                        <button onclick="cetakStruk()" class="btn btn-primary btn-lg">
                            <i class="fas fa-print me-2"></i> Cetak Struk
                        </button>
                        <a href="{{ route('parkir.keluar') }}" class="btn btn-success">
                            <i class="fas fa-plus-circle me-2"></i> Transaksi Baru
                        </a>
                        <a href="{{ route('parkir.masuk') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i> Kembali
                        </a>
                    </div>
                </div>

            </div>

        </div>
    </div>
</div>

<style>
.success-header { animation: fadeInDown 0.6s ease-out; }
.success-icon { font-size: 5rem; color: #28a745; animation: bounceIn 0.8s ease-out; }
@keyframes fadeInDown { from { opacity: 0; transform: translateY(-20px); } to { opacity: 1; transform: translateY(0); } }
@keyframes bounceIn { 0% { transform: scale(0); opacity: 0; } 50% { transform: scale(1.2); } 100% { transform: scale(1); opacity: 1; } }
.struk-card { border-radius: 20px; animation: slideUp 0.6s ease-out; }
@keyframes slideUp { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
.struk-header { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); border-radius: 20px 20px 0 0; }
.company-info { color: #333; }
.section-title { color: #667eea; }
.info-table td { padding: 8px 0; color: #555; }
.payment-table { margin-top: 10px; }
.payment-table td { padding: 12px 10px; }
.discount-row { background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%); }
.total-row { background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%); }
.info-box { background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); border-radius: 15px; padding: 20px; }
.footer-info { color: #666; }
.btn-primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; border-radius: 10px; }
.btn-success { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); border: none; border-radius: 10px; }
.btn:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(0,0,0,0.2); transition: all 0.3s ease; }
@media print {
    body * { visibility: hidden; }
    #strukCard, #strukCard * { visibility: visible; }
    #strukCard { position: absolute; left: 0; top: 0; width: 100%; box-shadow: none !important; }
    .no-print { display: none !important; }
    .struk-header { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%) !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    .discount-row { background: #d4edda !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    .total-row { background: #fff3cd !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    .info-box { background: #f8f9fa !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
}
@media (max-width: 768px) {
    .success-icon { font-size: 3rem; }
    .info-table td { font-size: 0.9rem; }
}
</style>

<script>
function cetakStruk() {
    window.print();
}
console.log('Struk loaded successfully');
</script>
@endsection