@extends('app')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-gradient-blue text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-users me-2"></i>Data Member & Kendaraan
                    </h4>
                </div>
                <div class="card-body">
                    
                    <!-- FORM FILTER -->
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">
                                <i class="fas fa-filter me-2"></i>Filter Pencarian
                            </h6>
                        </div>
                        <div class="card-body">
                            <form method="GET">
                                <div class="row g-3">
                                    
                                    <!-- Filter Nama -->
                                    <div class="col-md-3">
                                        <label class="form-label">Nama Pemilik</label>
                                        <input type="text" 
                                               name="nama" 
                                               class="form-control" 
                                               placeholder="Cari nama..." 
                                               value="{{ request('nama') }}">
                                    </div>

                                    <!-- Filter No HP -->
                                    <div class="col-md-3">
                                        <label class="form-label">No HP</label>
                                        <input type="text" 
                                               name="no_hp" 
                                               class="form-control" 
                                               placeholder="Cari no HP..." 
                                               value="{{ request('no_hp') }}">
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

                                    <!-- Filter Level Member -->
                                    <div class="col-md-3">
                                        <label class="form-label">Level Member</label>
                                        <select name="id_level" class="form-select">
                                            <option value="">Semua Level</option>
                                            @foreach($memberLevels as $level)
                                                <option value="{{ $level->id_level }}" 
                                                    {{ request('id_level') == $level->id_level ? 'selected' : '' }}>
                                                    {{ $level->nama_level }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <!-- Filter Status Member -->
                                    <div class="col-md-3">
                                        <label class="form-label">Status Member</label>
                                        <select name="status_member" class="form-select">
                                            <option value="">Semua Status</option>
                                            <option value="aktif" {{ request('status_member') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                                            <option value="expired" {{ request('status_member') == 'expired' ? 'selected' : '' }}>Expired</option>
                                        </select>
                                    </div>

                                    <!-- Tombol -->
                                    <div class="col-12">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-search me-1"></i>Cari
                                        </button>
                                        <a href="{{ route('master-data.member-kendaraan') }}" 
                                           class="btn btn-secondary">
                                            <i class="fas fa-redo me-1"></i>Reset
                                        </a>
                                    </div>

                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- TAB NAVIGATION -->
                    <ul class="nav nav-tabs mb-4" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link {{ $activeTab === 'member' ? 'active' : '' }}" 
                               href="#member-tab" 
                               data-bs-toggle="tab"
                               id="member-tab-link">
                                <i class="fas fa-id-card me-1"></i>
                                Pemilik dengan Member
                                <span class="badge bg-success ms-1">{{ $pemilikMember->total() }}</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ $activeTab === 'non-member' ? 'active' : '' }}" 
                               href="#non-member-tab" 
                               data-bs-toggle="tab"
                               id="non-member-tab-link">
                                <i class="fas fa-user me-1"></i>
                                Pemilik Tanpa Member
                                <span class="badge bg-secondary ms-1">{{ $pemilikNonMember->total() }}</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ $activeTab === 'kendaraan' ? 'active' : '' }}" 
                               href="#kendaraan-tanpa-tab" 
                               data-bs-toggle="tab"
                               id="kendaraan-tab-link">
                                <i class="fas fa-car me-1"></i>
                                Kendaraan Tanpa Pemilik
                                <span class="badge bg-warning ms-1">{{ $kendaraanTanpaPemilik->total() }}</span>
                            </a>
                        </li>
                    </ul>

                    <!-- TAB CONTENT -->
                    <div class="tab-content">
                        
                        <!-- TAB 1: PEMILIK DENGAN MEMBER -->
                        <div id="member-tab" class="tab-pane fade {{ $activeTab === 'member' ? 'show active' : '' }}">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead class="table-success">
                                        <tr>
                                            <th width="5%">No</th>
                                            <th>Nama Pemilik</th>
                                            <th>Kontak</th>
                                            <th>Level Member</th>
                                            <th>Status</th>
                                            <th>Berlaku Hingga</th>
                                            <th>Kendaraan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($pemilikMember as $index => $pemilik)
                                        <tr>
                                            <td class="text-center">{{ $pemilikMember->firstItem() + $index }}</td>
                                            <td>
                                                <strong>{{ $pemilik->nama }}</strong>
                                            </td>
                                            <td>
                                                <i class="fas fa-phone me-1"></i>{{ $pemilik->no_hp }}
                                                <br>
                                                <small class="text-muted">{{ $pemilik->alamat }}</small>
                                            </td>
                                            <td>
                                                @foreach($pemilik->members as $member)
                                                    <span class="badge bg-primary">
                                                        {{ $member->level->nama_level ?? '-' }}
                                                    </span>
                                                    <br>
                                                    <small class="text-muted">
                                                        Diskon: {{ $member->level->diskon_persen ?? 0 }}%
                                                    </small>
                                                @endforeach
                                            </td>
                                            <td>
                                                @foreach($pemilik->members as $member)
                                                    @if($member->status == 'aktif')
                                                        <span class="badge bg-success">Aktif</span>
                                                    @else
                                                        <span class="badge bg-danger">Expired</span>
                                                    @endif
                                                @endforeach
                                            </td>
                                            <td>
                                                @foreach($pemilik->members as $member)
                                                    <small>{{ $member->berlaku_hingga ? $member->berlaku_hingga->format('d/m/Y') : '-' }}</small>
                                                @endforeach
                                            </td>
                                            <td>
                                                @if($pemilik->kendaraan->count() > 0)
                                                    <div class="badge bg-info mb-2">
                                                        Total: {{ $pemilik->kendaraan->count() }} Kendaraan
                                                    </div>
                                                    <div class="small">
                                                        @foreach($pemilik->kendaraan as $kendaraan)
                                                            <div class="mb-1">
                                                                <i class="fas fa-car me-1"></i>
                                                                <strong>{{ $kendaraan->plat_nomor }}</strong>
                                                                <span class="text-muted">
                                                                    ({{ $kendaraan->tipe->tipe_kendaraan ?? '-' }})
                                                                </span>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @else
                                                    <span class="text-muted">Belum ada kendaraan</span>
                                                @endif
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="7" class="text-center text-muted">
                                                Tidak ada data pemilik dengan member
                                            </td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            
                            <!-- Pagination -->
                            <div class="mt-3">
                                {{ $pemilikMember->appends(request()->except('non_member_page', 'kendaraan_page'))->links() }}
                            </div>
                        </div>

                        <!-- TAB 2: PEMILIK TANPA MEMBER -->
                        <div id="non-member-tab" class="tab-pane fade {{ $activeTab === 'non-member' ? 'show active' : '' }}">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead class="table-secondary">
                                        <tr>
                                            <th width="5%">No</th>
                                            <th>Nama Pemilik</th>
                                            <th>Kontak</th>
                                            <th>Kendaraan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($pemilikNonMember as $index => $pemilik)
                                        <tr>
                                            <td class="text-center">{{ $pemilikNonMember->firstItem() + $index }}</td>
                                            <td>
                                                <strong>{{ $pemilik->nama }}</strong>
                                            </td>
                                            <td>
                                                <i class="fas fa-phone me-1"></i>{{ $pemilik->no_hp }}
                                                <br>
                                                <small class="text-muted">{{ $pemilik->alamat }}</small>
                                            </td>
                                            <td>
                                                @if($pemilik->kendaraan->count() > 0)
                                                    <div class="badge bg-info mb-2">
                                                        Total: {{ $pemilik->kendaraan->count() }} Kendaraan
                                                    </div>
                                                    <div class="small">
                                                        @foreach($pemilik->kendaraan as $kendaraan)
                                                            <div class="mb-1">
                                                                <i class="fas fa-car me-1"></i>
                                                                <strong>{{ $kendaraan->plat_nomor }}</strong>
                                                                <span class="text-muted">
                                                                    ({{ $kendaraan->tipe->tipe_kendaraan ?? '-' }})
                                                                </span>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @else
                                                    <span class="text-muted">Belum ada kendaraan</span>
                                                @endif
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="4" class="text-center text-muted">
                                                Tidak ada data pemilik tanpa member
                                            </td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            
                            <!-- Pagination -->
                            <div class="mt-3">
                                {{ $pemilikNonMember->appends(request()->except('member_page', 'kendaraan_page'))->links() }}
                            </div>
                        </div>

                        <!-- TAB 3: KENDARAAN TANPA PEMILIK -->
                        <div id="kendaraan-tanpa-tab" class="tab-pane fade {{ $activeTab === 'kendaraan' ? 'show active' : '' }}">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead class="table-warning">
                                        <tr>
                                            <th width="5%">No</th>
                                            <th>Plat Nomor</th>
                                            <th>Tipe Kendaraan</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($kendaraanTanpaPemilik as $index => $kendaraan)
                                        <tr>
                                            <td class="text-center">{{ $kendaraanTanpaPemilik->firstItem() + $index }}</td>
                                            <td>
                                                <strong>{{ $kendaraan->plat_nomor }}</strong>
                                            </td>
                                            <td>
                                                {{ $kendaraan->tipe->tipe_kendaraan ?? '-' }}
                                            </td>
                                            <td>
                                                @if($kendaraan->status === 'aktif')
                                                    <span class="badge bg-success">Aktif</span>
                                                @else
                                                    <span class="badge bg-secondary">Nonaktif</span>
                                                @endif
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="4" class="text-center text-muted">
                                                Tidak ada kendaraan tanpa pemilik
                                            </td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            
                            <!-- Pagination -->
                            <div class="mt-3">
                                {{ $kendaraanTanpaPemilik->appends(request()->except('member_page', 'non_member_page'))->links() }}
                            </div>
                        </div>

                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .nav-tabs .nav-link {
        color: #495057;
        font-weight: 500;
    }
    .nav-tabs .nav-link.active {
        color: #0d6efd;
        font-weight: 600;
    }
    .table th {
        font-weight: 600;
        white-space: nowrap;
    }
    .badge {
        font-size: 0.85em;
    }
    .bg-gradient-blue {
        background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
        color: white;
    }
</style>

{{-- NO JAVASCRIPT NEEDED - Tab handled by server-side $activeTab variable --}}
@endsection