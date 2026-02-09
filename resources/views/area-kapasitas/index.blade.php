@extends('app')

@section('content')
<div class="container-fluid">
    <h4>📍 Kapasitas Area Parkir</h4>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <a href="{{ route('area-kapasitas.create') }}" class="btn btn-primary mb-3 mt-4">
        <i class="fas fa-plus"></i> Tambah Kapasitas
    </a>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th width="5%">No</th>
                            <th>Area Parkir</th>
                            <th>Tipe Kendaraan</th>
                            <th width="15%" class="text-center">Kapasitas</th>
                            <th width="15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <i class="fas fa-map-marker-alt text-primary me-2"></i>
                                <strong>{{ $item->area->lokasi ?? '-' }}</strong>
                            </td>
                            <td>
                                <span class="badge bg-info">
                                    {{ $item->tipe->tipe_kendaraan ?? '-' }}
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-{{ $item->kapasitas > 10 ? 'success' : 'warning' }} fs-6">
                                    {{ $item->kapasitas }} slot
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('area-kapasitas.edit', $item->id_kapasitas) }}"
                                   class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit"></i>
                                </a>

                                <form action="{{ route('area-kapasitas.destroy', $item->id_kapasitas) }}"
                                      method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger btn-sm"
                                        onclick="return confirm('Hapus data kapasitas ini?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">
                                <i class="fas fa-inbox fa-2x mb-2"></i><br>
                                Belum ada data kapasitas area
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