@extends('app')

@section('content')
<div class="container-fluid">
    <h4>🗑️ Backup Data Tipe Kendaraan</h4>

    <div class="mb-3 mt-4">
        <a href="{{ route('tipe-kendaraan.index') }}" class="btn btn-primary">
            <i class="fas fa-arrow-left"></i> Kembali
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
                            <th>Dihapus Pada</th>
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
                            <td>{{ $item->deleted_at->format('d/m/Y H:i') }}</td>
                            <td>
                                <form action="{{ route('tipe-kendaraan.restore', $item->id_tipe) }}" 
                                      method="POST"
                                      id="restore-form-{{ $item->id_tipe }}">
                                    @csrf
                                    <button type="button"
                                            onclick="confirmRestore('Tipe Kendaraan', '{{ $item->tipe_kendaraan }}', 'restore-form-{{ $item->id_tipe }}')"
                                            class="btn btn-success btn-sm">
                                        <i class="fas fa-undo"></i> Pulihkan
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">
                                <i class="fas fa-check-circle fa-2x mb-2"></i><br>
                                Tidak ada data yang dihapus
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