@extends('app')

@section('title', 'Parkir Masuk')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-9">

            {{-- HEADER JAM --}}
            <div class="card shadow-sm mb-3 border-0" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="card-body text-white">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h3 class="mb-1">
                                <i class="fas fa-car-side"></i> Transaksi Parkir Masuk
                            </h3>
                            <p class="mb-0 opacity-75">Silakan input data kendaraan</p>
                        </div>
                        <div class="col-md-4 text-end">
                            <div class="bg-white bg-opacity-25 rounded p-3">
                                <div class="fs-5 fw-bold" id="jamSekarang">--:--:--</div>
                                <div class="small" id="tanggalSekarang">Loading...</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- CARD FORM --}}
            <div class="card shadow border-0">
                <div class="card-body p-4">

                    {{-- FLASH MESSAGE --}}
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show">
                            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                            <button class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form action="{{ route('parkir.masuk.store') }}" method="POST" id="formParkir">
                        @csrf

                        {{-- INPUT PLAT --}}
                        <div class="mb-4 position-relative">
                            <label class="form-label fw-bold">
                                <i class="fas fa-id-card text-primary"></i> Plat Nomor
                                <span class="text-danger">*</span>
                            </label>
                            
                            <input
                                type="text"
                                id="platInput"
                                name="plat_nomor"
                                class="form-control form-control-lg"
                                placeholder="Ketik minimal 2 karakter"
                                value="{{ old('plat_nomor') }}"
                                autocomplete="off"
                                required
                            >

                            {{-- DROPDOWN --}}
                            <ul id="platDropdown" class="list-group position-absolute w-100 shadow-lg" style="z-index: 1050; display: none; max-height: 300px; overflow-y: auto;"></ul>

                            @error('plat_nomor')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror

                            <small class="text-muted">
                                <i class="fas fa-info-circle"></i> Autocomplete aktif
                            </small>

                            <div id="statusKendaraan" class="mt-2"></div>
                        </div>

                        {{-- DROPDOWN TIPE --}}
                        <div class="mb-4">
                            <label class="form-label fw-bold">
                                <i class="fas fa-motorcycle text-success"></i> Tipe Kendaraan
                                <span class="text-danger">*</span>
                            </label>

                            {{-- HIDDEN INPUT untuk kirim data saat disabled --}}
                            <input type="hidden" id="tipeHidden" name="id_tipe" value="{{ old('id_tipe') }}">

                            <select
                                id="tipeSelect"
                                class="form-select form-select-lg"
                                required
                            >
                                <option value="">-- Pilih Tipe --</option>
                                @foreach($tipe as $t)
                                    <option value="{{ $t->id_tipe }}" {{ old('id_tipe') == $t->id_tipe ? 'selected' : '' }}>
                                        {{ $t->tipe_kendaraan }}
                                    </option>
                                @endforeach
                            </select>

                            @error('id_tipe')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- INFO SLOT --}}
                        <div id="infoSlot" style="display: none;">
                            <div class="alert alert-info">
                                <h6><i class="fas fa-parking"></i> Slot Tersedia</h6>
                                <div id="slotDetail"></div>
                            </div>
                        </div>

                        {{-- TOMBOL --}}
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg" id="btnSubmit">
                                <i class="fas fa-save"></i> <span id="btnText">Simpan & Cetak Tiket</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- KETERSEDIAAN SLOT --}}
            <div class="card shadow border-0 mt-3">
                <div class="card-header text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <h5 class="mb-0"><i class="fas fa-chart-bar"></i> Ketersediaan Slot</h5>
                </div>
                <div class="card-body">
                    @if($kapasitas->isEmpty())
                        <div class="alert alert-warning">Tidak ada slot tersedia</div>
                    @else
                        <div class="row">
                            @foreach($kapasitas as $idTipe => $areas)
                                <div class="col-md-6 mb-3">
                                    <h6 class="fw-bold text-primary">
                                        {{ $areas->first()->tipe->tipe_kendaraan }}
                                    </h6>
                                    @foreach($areas as $area)
                                        <div class="d-flex justify-content-between mb-2">
                                            <span>{{ $area->area->lokasi }}</span>
                                            <span class="badge bg-{{ $area->kapasitas > 10 ? 'success' : 'warning' }}">
                                                {{ $area->kapasitas }} slot
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</div>

<script>
(function() {
    'use strict';
    
    console.log('=== PARKIR SCRIPT START ===');
    
    var kapasitasData = {!! json_encode($kapasitas) !!};
    var autocompleteUrl = "{{ route('parkir.masuk.autocomplete.plat') }}";
    
    // ==================
    // JAM REAL-TIME
    // ==================
    function updateJam() {
        var now = new Date();
        var jam = String(now.getHours()).padStart(2, '0');
        var menit = String(now.getMinutes()).padStart(2, '0');
        var detik = String(now.getSeconds()).padStart(2, '0');
        
        var hari = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        var bulan = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agt', 'Sep', 'Okt', 'Nov', 'Des'];
        
        var elemJam = document.getElementById('jamSekarang');
        var elemTanggal = document.getElementById('tanggalSekarang');
        
        if (elemJam) elemJam.textContent = jam + ':' + menit + ':' + detik;
        if (elemTanggal) {
            elemTanggal.textContent = hari[now.getDay()] + ', ' + now.getDate() + ' ' + bulan[now.getMonth()] + ' ' + now.getFullYear();
        }
    }
    
    setInterval(updateJam, 1000);
    updateJam();
    
    // ==================
    // ELEMENTS
    // ==================
    var platInput = document.getElementById('platInput');
    var platDropdown = document.getElementById('platDropdown');
    var tipeSelect = document.getElementById('tipeSelect');
    var tipeHidden = document.getElementById('tipeHidden');
    var statusDiv = document.getElementById('statusKendaraan');
    var formParkir = document.getElementById('formParkir');
    var btnSubmit = document.getElementById('btnSubmit');
    var btnText = document.getElementById('btnText');
    
    var typingTimer;
    var isKendaraanTerdaftar = false;
    
    // ==================
    // AUTO UPPERCASE
    // ==================
    platInput.addEventListener('input', function() {
        this.value = this.value.toUpperCase();
    });
    
    // ==================
    // SYNC SELECT & HIDDEN
    // ==================
    tipeSelect.addEventListener('change', function() {
        tipeHidden.value = this.value;
        console.log('Tipe changed:', this.value);
    });
    
    // ==================
    // AUTOCOMPLETE
    // ==================
    platInput.addEventListener('keyup', function() {
        var keyword = this.value.trim();
        
        clearTimeout(typingTimer);
        
        // Reset state
        isKendaraanTerdaftar = false;
        
        if (keyword.length < 1) {
            platDropdown.style.display = 'none';
            statusDiv.innerHTML = '';
            tipeSelect.disabled = false;
            tipeSelect.value = '';
            tipeHidden.value = '';
            return;
        }
        
        typingTimer = setTimeout(function() {
            cariPlat(keyword);
        }, 300);
    });
    
    function cariPlat(keyword) {
        var url = autocompleteUrl + '?q=' + encodeURIComponent(keyword);
        
        platDropdown.innerHTML = '<li class="list-group-item text-center"><i class="fas fa-spinner fa-spin"></i> Mencari...</li>';
        platDropdown.style.display = 'block';
        
        fetch(url)
            .then(function(response) {
                if (!response.ok) throw new Error('HTTP ' + response.status);
                return response.json();
            })
            .then(function(data) {
                platDropdown.innerHTML = '';
                
                if (data.length === 0) {
                    platDropdown.innerHTML = '<li class="list-group-item text-muted">Tidak ditemukan</li>';
                    
                    statusDiv.innerHTML = '<div class="alert alert-warning mt-2"><i class="fas fa-plus-circle"></i> <strong>Kendaraan Baru</strong><br>Pilih tipe kendaraan</div>';
                    
                    tipeSelect.disabled = false;
                    isKendaraanTerdaftar = false;
                    
                    setTimeout(function() {
                        platDropdown.style.display = 'none';
                    }, 2000);
                    
                    return;
                }
                
                data.forEach(function(item) {
                    var li = document.createElement('li');
                    li.className = 'list-group-item list-group-item-action';
                    li.style.cursor = 'pointer';
                    li.innerHTML = '<strong>' + item.plat_nomor + '</strong><br><small>' + item.tipe_kendaraan + '</small>';
                    
                    li.addEventListener('click', function() {
                        pilihPlat(item);
                    });
                    
                    platDropdown.appendChild(li);
                });
                
                platDropdown.style.display = 'block';
            })
            .catch(function(error) {
                console.error('Error:', error);
                platDropdown.innerHTML = '<li class="list-group-item text-danger">Error</li>';
            });
    }
    
    function pilihPlat(item) {
        console.log('Plat dipilih:', item);
        
        isKendaraanTerdaftar = true;
        
        platInput.value = item.plat_nomor;
        
        // Set select dan hidden
        tipeSelect.value = item.id_tipe;
        tipeHidden.value = item.id_tipe;
        
        // Disable select (tapi hidden tetap aktif)
        tipeSelect.disabled = true;
        
        platDropdown.style.display = 'none';
        
        statusDiv.innerHTML = '<div class="alert alert-success mt-2"><i class="fas fa-check-circle"></i> <strong>Kendaraan Terdaftar</strong><br>' + item.plat_nomor + ' - ' + item.tipe_kendaraan + '</div>';
        
        // Trigger change untuk tampilkan slot
        tipeSelect.dispatchEvent(new Event('change'));
    }
    
    // Close dropdown
    document.addEventListener('click', function(e) {
        if (e.target !== platInput && !platDropdown.contains(e.target)) {
            platDropdown.style.display = 'none';
        }
    });
    
    // ==================
    // TAMPILKAN SLOT
    // ==================
    tipeSelect.addEventListener('change', function() {
        var idTipe = this.value;
        var infoSlot = document.getElementById('infoSlot');
        var slotDetail = document.getElementById('slotDetail');
        
        if (!idTipe || !kapasitasData[idTipe]) {
            infoSlot.style.display = 'none';
            return;
        }
        
        var areas = kapasitasData[idTipe];
        var total = 0;
        var html = '<ul>';
        
        areas.forEach(function(area) {
            total += area.kapasitas;
            html += '<li><strong>' + area.area.lokasi + '</strong>: ' + area.kapasitas + ' slot</li>';
        });
        
        html += '</ul><strong>Total: ' + total + ' slot</strong>';
        
        slotDetail.innerHTML = html;
        infoSlot.style.display = 'block';
    });
    
    // ==================
    // FORM SUBMIT
    // ==================
    formParkir.addEventListener('submit', function(e) {
        console.log('Form submitting...');
        console.log('Plat:', platInput.value);
        console.log('Tipe Select:', tipeSelect.value);
        console.log('Tipe Hidden:', tipeHidden.value);
        
        // Pastikan hidden input terisi
        if (!tipeHidden.value) {
            tipeHidden.value = tipeSelect.value;
        }
        
        // Validasi
        if (!platInput.value.trim()) {
            e.preventDefault();
            alert('Plat nomor harus diisi!');
            platInput.focus();
            return false;
        }
        
        if (!tipeHidden.value) {
            e.preventDefault();
            alert('Tipe kendaraan harus dipilih!');
            tipeSelect.focus();
            return false;
        }
        
        // Disable button
        btnSubmit.disabled = true;
        btnText.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memproses...';
        
        console.log('Form valid, submitting...');
    });
    
    console.log('=== SCRIPT READY ===');
    
})();
</script>
@endsection