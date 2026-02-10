@extends('app')

@section('content')
<div class="container-fluid">
    <h4>🚗 Data Tipe Kendaraan</h4>

    <div class="text-end mb-3 mt-4">
        <button onclick="confirmCreate('Tipe Kendaraan', '{{ route('tipe-kendaraan.create') }}')" class="btn btn-primary">
            <i class="fas fa-plus"></i> Tambah Tipe Kendaraan
        </button>
        <a href="{{ route('tipe-kendaraan.trash') }}" class="btn btn-secondary">
            <i class="fas fa-trash"></i> Backup Data
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th width="50">No</th>
                            <th>Kode Tipe</th>
                            <th>Tipe Kendaraan</th>
                            <th>Deskripsi</th>
                            <th width="150">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tipeKendaraan as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td><span class="badge bg-info">{{ $item->kode_tipe }}</span></td>
                            <td><strong>{{ $item->tipe_kendaraan }}</strong></td>
                            <td>{{ $item->deskripsi_tipe ?? '-' }}</td>
                            <td>
                                <button onclick="confirmEdit('Tipe Kendaraan', '{{ $item->tipe_kendaraan }}', '{{ route('tipe-kendaraan.edit', $item) }}')" 
                                        class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit"></i>
                                </button>

                                <form action="{{ route('tipe-kendaraan.destroy', $item) }}" 
                                      method="POST" 
                                      class="d-inline"
                                      id="delete-form-{{ $item->id_tipe }}">
                                    @csrf @method('DELETE')
                                    <button type="button"
                                            onclick="confirmDelete('Tipe Kendaraan', '{{ $item->tipe_kendaraan }}', 'delete-form-{{ $item->id_tipe }}')"
                                            class="btn btn-danger btn-sm">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">
                                <i class="fas fa-inbox fa-2x mb-2"></i><br>
                                Belum ada data tipe kendaraan
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