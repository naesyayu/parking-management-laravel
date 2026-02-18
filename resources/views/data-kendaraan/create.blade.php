@extends('app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-car"></i> Tambah Kendaraan Baru</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('data-kendaraan.store') }}" id="formKendaraan">
                        @csrf

                        {{-- PLAT NOMOR --}}
                        <div class="mb-3">
                            <label class="form-label fw-bold">
                                <i class="fas fa-id-card text-primary"></i> Plat Nomor 
                                <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   id="platInput"
                                   name="plat_nomor" 
                                   class="form-control form-control-lg @error('plat_nomor') is-invalid @enderror"
                                   value="{{ old('plat_nomor') }}"
                                   placeholder="Contoh: B 1234 ABC"
                                   required
                                   autocomplete="off">
                            
                            @error('plat_nomor')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            
                            <div id="platValidation" class="mt-2"></div>
                            
                            <small class="text-muted">
                                <i class="fas fa-info-circle"></i> 
                                Format: 1-2 huruf, spasi, 1-4 angka, spasi, 1-3 huruf (Contoh: <strong>B 1234 ABC</strong>, <strong>DK 567 XY</strong>)
                            </small>
                        </div>

                        {{-- PEMILIK --}}
                        <div class="mb-3">
                            <label class="form-label fw-bold">
                                <i class="fas fa-user text-success"></i> Pemilik (Opsional)
                            </label>
                            <select name="id_pemilik" 
                                    class="form-select @error('id_pemilik') is-invalid @enderror">
                                <option value="">-- Tanpa Pemilik --</option>
                                @foreach($pemiliks as $p)
                                    <option value="{{ $p->id_pemilik }}" {{ old('id_pemilik') == $p->id_pemilik ? 'selected' : '' }}>
                                        {{ $p->nama }}
                                    </option>
                                @endforeach
                            </select>
                            @error('id_pemilik')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- TIPE KENDARAAN --}}
                        <div class="mb-3">
                            <label class="form-label fw-bold">
                                <i class="fas fa-motorcycle text-warning"></i> Tipe Kendaraan 
                                <span class="text-danger">*</span>
                            </label>
                            <select name="id_tipe" 
                                    class="form-select @error('id_tipe') is-invalid @enderror" 
                                    required>
                                <option value="">-- Pilih Tipe --</option>
                                @foreach($tipes as $t)
                                    <option value="{{ $t->id_tipe }}" {{ old('id_tipe') == $t->id_tipe ? 'selected' : '' }}>
                                        {{ $t->tipe_kendaraan }}
                                    </option>
                                @endforeach
                            </select>
                            @error('id_tipe')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- STATUS --}}
                        <div class="mb-3">
                            <label class="form-label fw-bold">
                                <i class="fas fa-toggle-on text-info"></i> Status 
                                <span class="text-danger">*</span>
                            </label>
                            <select name="status" 
                                    class="form-select @error('status') is-invalid @enderror" 
                                    required>
                                <option value="aktif" {{ old('status', 'aktif') == 'aktif' ? 'selected' : '' }}>
                                    Aktif
                                </option>
                                <option value="nonaktif" {{ old('status') == 'nonaktif' ? 'selected' : '' }}>
                                    Nonaktif
                                </option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr>

                        {{-- TOMBOL --}}
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary" id="btnSubmit">
                                <i class="fas fa-save"></i> Simpan
                            </button>
                            <a href="{{ route('data-kendaraan.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Kembali
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            {{-- INFO BOX --}}
            <div class="alert alert-info mt-3" role="alert">
                <h6><i class="fas fa-info-circle"></i> Format Plat Nomor Indonesia:</h6>
                <ul class="mb-0">
                    <li><strong>Kode Area:</strong> 1-2 huruf (contoh: B, DK, D)</li>
                    <li><strong>Nomor Seri:</strong> 1-4 angka (contoh: 1, 123, 1234)</li>
                    <li><strong>Kode Seri:</strong> 1-3 huruf (contoh: A, XY, ABC)</li>
                </ul>
                <hr>
                <small class="text-muted">
                    <strong>Contoh Valid:</strong> B 1234 ABC, DK 567 XY, D 1 A, KB 8888 ZZZ
                </small>
            </div>
        </div>
    </div>
</div>

<script>
(function() {
    'use strict';
    
    console.log('=== KENDARAAN CREATE - AUTO FORMAT START ===');
    
    var platInput = document.getElementById('platInput');
    var platValidation = document.getElementById('platValidation');
    var formKendaraan = document.getElementById('formKendaraan');
    var btnSubmit = document.getElementById('btnSubmit');
    
    // ==========================================
    // AUTO-FORMAT PLAT NOMOR
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
            platValidation.innerHTML = '';
            return true;
        }
        
        // Pattern: [1-2 letters] [SPACE] [1-4 numbers] [SPACE] [1-3 letters]
        var pattern = /^[A-Z]{1,2}\s\d{1,4}\s[A-Z]{1,3}$/;
        
        if (pattern.test(plat)) {
            // Valid format
            platValidation.innerHTML = '<div class="alert alert-success alert-sm mt-2"><i class="fas fa-check-circle"></i> Format plat nomor valid</div>';
            return true;
        } else {
            // Invalid or incomplete
            var parts = plat.split(' ');
            var hints = [];
            
            if (parts.length < 3) {
                hints.push('Format lengkap: <strong>HURUF ANGKA HURUF</strong>');
            }
            
            if (parts[0] && !/^[A-Z]{1,2}$/.test(parts[0])) {
                hints.push('Kode area: 1-2 huruf');
            }
            
            if (parts[1] && !/^\d{1,4}$/.test(parts[1])) {
                hints.push('Nomor seri: 1-4 angka');
            }
            
            if (parts[2] && !/^[A-Z]{1,3}$/.test(parts[2])) {
                hints.push('Kode seri: 1-3 huruf');
            }
            
            if (hints.length > 0) {
                platValidation.innerHTML = '<div class="alert alert-warning alert-sm mt-2"><i class="fas fa-info-circle"></i> ' + hints.join(' • ') + '<br><small class="text-muted">Contoh: <strong>B 1234 ABC</strong> atau <strong>DK 567 XY</strong></small></div>';
            }
            
            return false;
        }
    }
    
    // ==========================================
    // FORM SUBMIT VALIDATION
    // ==========================================
    formKendaraan.addEventListener('submit', function(e) {
        console.log('Form submitting...');
        console.log('Plat:', platInput.value);
        
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
        
        // Disable button
        btnSubmit.disabled = true;
        btnSubmit.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';
        
        console.log('Form valid, submitting...');
    });
    
    // Initial validation on page load (for old input)
    if (platInput.value) {
        validatePlatFormat(platInput.value);
    }
    
    console.log('=== KENDARAAN CREATE - AUTO FORMAT READY ===');
    
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