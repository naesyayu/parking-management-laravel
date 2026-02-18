@extends('app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card shadow-sm">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0"><i class="fas fa-edit"></i> Edit Member</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('member.update', $member->id_member) }}">
                        @csrf 
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label fw-bold">
                                <i class="fas fa-user"></i> Pemilik 
                                <span class="text-danger">*</span>
                            </label>
                            <select name="id_pemilik" 
                                    class="form-select @error('id_pemilik') is-invalid @enderror"
                                    required>
                                <option value="">-- Pilih Pemilik --</option>
                                @foreach($pemiliks as $p)
                                    <option value="{{ $p->id_pemilik }}"
                                            {{ (old('id_pemilik', $member->id_pemilik) == $p->id_pemilik) ? 'selected' : '' }}>
                                        {{ $p->nama }}
                                        @if($p->id_pemilik == $member->id_pemilik)
                                            <span class="text-muted">(Saat ini)</span>
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('id_pemilik')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">
                                <i class="fas fa-info-circle"></i> 
                                Pemilik saat ini: <strong>{{ $member->pemilik->nama }}</strong>
                            </small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">
                                <i class="fas fa-award"></i> Level Member 
                                <span class="text-danger">*</span>
                            </label>
                            <select name="id_level" 
                                    class="form-select @error('id_level') is-invalid @enderror"
                                    required>
                                <option value="">-- Pilih Level --</option>
                                @foreach($levels as $l)
                                    <option value="{{ $l->id_level }}"
                                            {{ (old('id_level', $member->id_level) == $l->id_level) ? 'selected' : '' }}>
                                        {{ $l->nama_level }} (Diskon {{ $l->diskon_persen }}%)
                                    </option>
                                @endforeach
                            </select>
                            @error('id_level')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">
                                        <i class="fas fa-calendar-check"></i> Berlaku Mulai 
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="date" 
                                           name="berlaku_mulai"
                                           value="{{ old('berlaku_mulai', $member->berlaku_mulai->format('Y-m-d')) }}"
                                           class="form-control @error('berlaku_mulai') is-invalid @enderror"
                                           min="{{ date('Y-m-d') }}"
                                           required>
                                    @error('berlaku_mulai')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">
                                        <i class="fas fa-info-circle"></i> 
                                        Minimal hari ini ({{ date('d/m/Y') }})
                                    </small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">
                                        <i class="fas fa-calendar-times"></i> Berlaku Hingga 
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="date" 
                                           name="berlaku_hingga"
                                           id="berlaku_hingga"
                                           value="{{ old('berlaku_hingga', $member->berlaku_hingga->format('Y-m-d')) }}"
                                           class="form-control @error('berlaku_hingga') is-invalid @enderror"
                                           min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                                           required>
                                    @error('berlaku_hingga')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">
                                        <i class="fas fa-info-circle"></i> 
                                        Harus setelah tanggal berlaku mulai
                                    </small>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">
                                <i class="fas fa-toggle-on"></i> Status 
                                <span class="text-danger">*</span>
                            </label>
                            <select name="status" 
                                    class="form-select @error('status') is-invalid @enderror"
                                    required>
                                <option value="aktif" {{ old('status', $member->status) == 'aktif' ? 'selected' : '' }}>
                                    Aktif
                                </option>
                                <option value="expired" {{ old('status', $member->status) == 'expired' ? 'selected' : '' }}>
                                    Expired
                                </option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Member
                            </button>
                            <a href="{{ route('member.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Kembali
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="alert alert-info mt-3" role="alert">
                <i class="fas fa-info-circle"></i>
                <strong>Catatan:</strong>
                <ul class="mb-0 mt-2">
                    <li>Satu pemilik hanya dapat memiliki satu membership aktif</li>
                    <li>Dropdown hanya menampilkan pemilik yang belum terdaftar + pemilik member saat ini</li>
                    <li>Jika mengganti pemilik, pastikan pemilik baru belum memiliki membership aktif</li>
                    <li><strong>Tanggal berlaku mulai tidak boleh sebelum hari ini</strong> (minimal hari ini untuk perpanjangan)</li>
                    <li>Tanggal berlaku hingga harus setelah tanggal berlaku mulai</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
(function() {
    'use strict';
    
    // Dynamic min date untuk berlaku_hingga berdasarkan berlaku_mulai
    const berlakuMulai = document.querySelector('input[name="berlaku_mulai"]');
    const berlakuHingga = document.getElementById('berlaku_hingga');
    
    if (berlakuMulai && berlakuHingga) {
        berlakuMulai.addEventListener('change', function() {
            if (this.value) {
                // Set min date berlaku_hingga = berlaku_mulai + 1 hari
                const mulaiDate = new Date(this.value);
                mulaiDate.setDate(mulaiDate.getDate() + 1);
                
                const minDate = mulaiDate.toISOString().split('T')[0];
                berlakuHingga.setAttribute('min', minDate);
                
                // Jika berlaku_hingga < berlaku_mulai, reset berlaku_hingga
                if (berlakuHingga.value && berlakuHingga.value <= this.value) {
                    berlakuHingga.value = minDate;
                }
            }
        });
        
        // Trigger on load jika berlaku_mulai sudah ada value
        if (berlakuMulai.value) {
            berlakuMulai.dispatchEvent(new Event('change'));
        }
    }
})();
</script>
@endsection