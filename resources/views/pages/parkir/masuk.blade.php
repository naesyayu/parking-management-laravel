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
                                placeholder="Ketik minimal 1 atau 2 karakter"
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

                        {{-- SELECT AREA (DYNAMIC BASED ON TIPE) --}}
                        <div id="areaSelectContainer" style="display: none;" class="mb-4">
                            <label class="form-label fw-bold">
                                <i class="fas fa-map-marker-alt text-warning"></i> Pilih Area Parkir
                                <span class="text-danger">*</span>
                            </label>
                            
                            <select id="areaSelect" name="id_area_manual" class="form-select form-select-lg">
                                <option value="">-- Pilih Area --</option>
                            </select>
                            
                            @error('id_area_manual')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                            
                            <small class="text-muted">
                                <i class="fas fa-info-circle"></i> Pilih area yang tersedia untuk tipe kendaraan ini
                            </small>
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
                                            <span>{{ $area->area->nama_area }}</span>
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
    
    console.log('=== PARKIR SCRIPT WITH AUTO-FORMAT START ===');
    
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
    var areaSelectContainer = document.getElementById('areaSelectContainer');
    var areaSelect = document.getElementById('areaSelect');
    var statusDiv = document.getElementById('statusKendaraan');
    var formParkir = document.getElementById('formParkir');
    var btnSubmit = document.getElementById('btnSubmit');
    var btnText = document.getElementById('btnText');
    
    var typingTimer;
    var isKendaraanTerdaftar = false;
    
    // ==========================================
    // AUTO-FORMAT PLAT NOMOR (INDONESIAN FORMAT)
    // Format: [1-2 huruf] [SPASI] [1-4 angka] [SPASI] [1-3 huruf]
    // Contoh: B 1234 ABC, DK 567 XY
    // ==========================================
    platInput.addEventListener('input', function(e) {
        var cursorPos = this.selectionStart;
        var value = this.value.toUpperCase();
        
        // Remove all spaces for processing
        var cleaned = value.replace(/\s/g, '');
        
        // Validate characters (only letters and numbers allowed)
        cleaned = cleaned.replace(/[^A-Z0-9]/g, '');
        
        // Apply formatting
        var formatted = '';
        var lettersFirst = '';
        var numbers = '';
        var lettersLast = '';
        
        // Phase 1: Extract first 1-2 letters (area code)
        var i = 0;
        while (i < cleaned.length && /[A-Z]/.test(cleaned[i]) && lettersFirst.length < 2) {
            lettersFirst += cleaned[i];
            i++;
        }
        
        // Phase 2: Extract 1-4 numbers
        while (i < cleaned.length && /[0-9]/.test(cleaned[i]) && numbers.length < 4) {
            numbers += cleaned[i];
            i++;
        }
        
        // Phase 3: Extract last 1-3 letters (series code)
        while (i < cleaned.length && /[A-Z]/.test(cleaned[i]) && lettersLast.length < 3) {
            lettersLast += cleaned[i];
            i++;
        }
        
        // Build formatted string
        if (lettersFirst) {
            formatted = lettersFirst;
        }
        
        if (numbers) {
            formatted += (formatted ? ' ' : '') + numbers;
        }
        
        if (lettersLast) {
            formatted += (formatted ? ' ' : '') + lettersLast;
        }
        
        // Update input value
        this.value = formatted;
        
        // Restore cursor position (approximate)
        var newCursorPos = cursorPos;
        if (formatted.length < value.length) {
            newCursorPos = Math.max(0, cursorPos - (value.length - formatted.length));
        }
        this.setSelectionRange(newCursorPos, newCursorPos);
        
        // Real-time validation feedback
        validatePlatFormat(formatted);
    });
    
    // ==========================================
    // VALIDATE PLAT FORMAT (REAL-TIME)
    // ==========================================
    function validatePlatFormat(plat) {
        if (!plat || plat.length === 0) {
            statusDiv.innerHTML = '';
            return true;
        }
        
        // Pattern: [1-2 letters] [SPACE] [1-4 numbers] [SPACE] [1-3 letters]
        var pattern = /^[A-Z]{1,2}\s\d{1,4}\s[A-Z]{1,3}$/;
        
        if (pattern.test(plat)) {
            // Valid format
            if (!isKendaraanTerdaftar) {
                statusDiv.innerHTML = '<div class="alert alert-success alert-sm mt-2"><i class="fas fa-check-circle"></i> Format plat nomor valid</div>';
            }
            return true;
        } else {
            // Invalid or incomplete
            var parts = plat.split(' ');
            var hints = [];
            
            if (parts.length < 3) {
                hints.push('Format: <strong>HURUF ANGKA HURUF</strong>');
            }
            
            if (parts[0] && !/^[A-Z]{1,2}$/.test(parts[0])) {
                hints.push('Kode area: 1-2 huruf');
            }
            
            if (parts[1] && !/^\d{1,4}$/.test(parts[1])) {
                hints.push('Nomor: 1-4 angka');
            }
            
            if (parts[2] && !/^[A-Z]{1,3}$/.test(parts[2])) {
                hints.push('Kode seri: 1-3 huruf');
            }
            
            if (hints.length > 0 && !isKendaraanTerdaftar) {
                statusDiv.innerHTML = '<div class="alert alert-warning alert-sm mt-2"><i class="fas fa-info-circle"></i> ' + hints.join(' • ') + '<br><small class="text-muted">Contoh: <strong>B 1234 ABC</strong> atau <strong>DK 567 XY</strong></small></div>';
            }
            
            return false;
        }
    }
    
    // ==================
    // SYNC SELECT & HIDDEN
    // ==================
    tipeSelect.addEventListener('change', function() {
        tipeHidden.value = this.value;
        console.log('Tipe changed:', this.value);
        
        if (this.value) {
            showAreaSelect(this.value);
        } else {
            areaSelectContainer.style.display = 'none';
        }
    });
    
    // ==================
    // SHOW AREA SELECT BASED ON TIPE
    // ==================
    function showAreaSelect(idTipe) {
        console.log('Showing areas for tipe:', idTipe);
        
        areaSelect.innerHTML = '<option value="">-- Pilih Area --</option>';
        
        if (!kapasitasData[idTipe] || kapasitasData[idTipe].length === 0) {
            areaSelectContainer.style.display = 'none';
            alert('Tidak ada slot tersedia untuk tipe kendaraan ini!');
            tipeSelect.value = '';
            tipeHidden.value = '';
            return;
        }
        
        kapasitasData[idTipe].forEach(function(area) {
            if (area.kapasitas > 0) {
                var option = document.createElement('option');
                option.value = area.id_area;
                option.textContent = area.area.nama_area + ' (' + area.kapasitas + ' slot)';
                areaSelect.appendChild(option);
            }
        });
        
        areaSelectContainer.style.display = 'block';
        
        if (areaSelect.options.length === 2) {
            areaSelect.selectedIndex = 1;
        }
    }
    
    // ==================
    // AUTOCOMPLETE
    // ==================
    platInput.addEventListener('keyup', function() {
        var keyword = this.value.trim();
        
        clearTimeout(typingTimer);
        
        isKendaraanTerdaftar = false;
        
        if (keyword.length < 2) {
            platDropdown.style.display = 'none';
            if (!keyword) {
                statusDiv.innerHTML = '';
            }
            tipeSelect.disabled = false;
            tipeSelect.value = '';
            tipeHidden.value = '';
            areaSelectContainer.style.display = 'none';
            return;
        }
        
        typingTimer = setTimeout(function() {
            cariPlat(keyword);
        }, 500);
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
                    
                    statusDiv.innerHTML = '<div class="alert alert-info mt-2"><i class="fas fa-plus-circle"></i> <strong>Kendaraan Baru</strong><br>Pastikan format plat sudah benar, lalu pilih tipe kendaraan</div>';
                    
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
        
        tipeSelect.value = item.id_tipe;
        tipeHidden.value = item.id_tipe;
        
        tipeSelect.disabled = true;
        
        platDropdown.style.display = 'none';
        
        statusDiv.innerHTML = '<div class="alert alert-success mt-2"><i class="fas fa-check-circle"></i> <strong>Kendaraan Terdaftar</strong><br>' + item.plat_nomor + ' - ' + item.tipe_kendaraan + '</div>';
        
        showAreaSelect(item.id_tipe);
    }
    
    document.addEventListener('click', function(e) {
        if (e.target !== platInput && !platDropdown.contains(e.target)) {
            platDropdown.style.display = 'none';
        }
    });
    
    // ==================
    // FORM SUBMIT WITH VALIDATION
    // ==================
    formParkir.addEventListener('submit', function(e) {
        console.log('Form submitting...');
        console.log('Plat:', platInput.value);
        console.log('Tipe:', tipeHidden.value);
        console.log('Area:', areaSelect.value);
        
        // Ensure tipeHidden has value
        if (!tipeHidden.value) {
            tipeHidden.value = tipeSelect.value;
        }
        
        // Validation 1: Plat nomor
        if (!platInput.value.trim()) {
            e.preventDefault();
            alert('Plat nomor harus diisi!');
            platInput.focus();
            return false;
        }
        
        // Validation 2: Plat format
        if (!validatePlatFormat(platInput.value.trim())) {
            e.preventDefault();
            alert('Format plat nomor tidak valid!\n\nFormat yang benar:\n• 1-2 huruf (kode area)\n• 1-4 angka\n• 1-3 huruf (kode seri)\n\nContoh: B 1234 ABC, DK 567 XY');
            platInput.focus();
            return false;
        }
        
        // Validation 3: Tipe
        if (!tipeHidden.value) {
            e.preventDefault();
            alert('Tipe kendaraan harus dipilih!');
            tipeSelect.focus();
            return false;
        }
        
        // Validation 4: Area (if shown)
        if (areaSelectContainer.style.display !== 'none' && !areaSelect.value) {
            e.preventDefault();
            alert('Area parkir harus dipilih!');
            areaSelect.focus();
            return false;
        }
        
        // Disable button
        btnSubmit.disabled = true;
        btnText.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memproses...';
        
        console.log('Form valid, submitting...');
    });
    
    console.log('=== SCRIPT WITH AUTO-FORMAT READY ===');
    
})();
</script>

<style>
/* Alert sizes */
.alert-sm {
    padding: 0.5rem 0.75rem;
    font-size: 0.875rem;
}
</style>
@endsection