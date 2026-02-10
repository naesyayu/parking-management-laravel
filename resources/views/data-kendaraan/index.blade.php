@extends('app')

@section('content')
<div class="container-fluid">
    <h4>🚗 Data Kendaraan</h4>

    {{-- SEARCH & BUTTONS --}}
    <div class="row mb-3 mt-4">
        <div class="col-md-6">
            <form method="GET" class="d-flex gap-2">
                <input type="text" 
                       name="search" 
                       class="form-control" 
                       placeholder="🔍 Cari plat nomor..." 
                       value="{{ request('search') }}">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i> Cari
                </button>
                @if(request('search'))
                    <a href="{{ route('data-kendaraan.index') }}" class="btn btn-secondary">
                        <i class="fas fa-redo"></i> Reset
                    </a>
                @endif
            </form>
        </div>
        <div class="col-md-6 text-end">
            <button onclick="confirmCreate('Kendaraan', '{{ route('data-kendaraan.create') }}')" class="btn btn-primary">
                <i class="fas fa-plus"></i> Tambah Kendaraan
            </button>
            <a href="{{ route('data-kendaraan.trash') }}" class="btn btn-secondary">
                <i class="fas fa-trash"></i> Backup Data
            </a>
        </div>
    </div>

    {{-- TABLE --}}
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th width="50">No</th>
                            <th>Plat Nomor</th>
                            <th>Pemilik</th>
                            <th>Tipe Kendaraan</th>
                            <th>Status</th>
                            <th width="150">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($kendaraans as $k)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td><strong>{{ $k->plat_nomor }}</strong></td>
                            <td>{{ $k->pemilik?->nama ?? '-' }}</td>
                            <td>{{ $k->tipe->tipe_kendaraan }}</td>
                            <td>
                                <span class="badge bg-{{ $k->status=='aktif'?'success':'secondary' }}">
                                    {{ ucfirst($k->status) }}
                                </span>
                            </td>
                            <td>
                                <button onclick="confirmEdit('Kendaraan', '{{ $k->plat_nomor }}', '{{ route('data-kendaraan.edit', $k->id_kendaraan) }}')" 
                                        class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit"></i>
                                </button>

                                <form action="{{ route('data-kendaraan.destroy', $k->id_kendaraan) }}" 
                                      method="POST" 
                                      class="d-inline"
                                      id="delete-form-{{ $k->id_kendaraan }}">
                                    @csrf @method('DELETE')
                                    <button type="button"
                                            onclick="confirmDelete('Kendaraan', '{{ $k->plat_nomor }}', 'delete-form-{{ $k->id_kendaraan }}')"
                                            class="btn btn-danger btn-sm">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">
                                <i class="fas fa-inbox fa-2x mb-2"></i><br>
                                @if(request('search'))
                                    Tidak ditemukan kendaraan dengan plat "{{ request('search') }}"
                                @else
                                    Belum ada data kendaraan
                                @endif
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection