@extends('app')

@section('content')
<div class="container-fluid">
    <h4>💰 Tarif Parkir</h4>

    <div class="text-end mb-3 mt-4">
        <a href="{{ route('tarif-parkir.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Tambah Tarif
        </a>
        <a href="{{ route('tarif-parkir.trash') }}" class="btn btn-secondary">
            <i class="fas fa-trash"></i> Backup Data
        </a>
    </div>

    <!-- GROUPED BY TIPE KENDARAAN -->
    @foreach($tipes as $tipe)
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">
                <i class="fas fa-{{ $tipe->tipe_kendaraan == 'Motor' ? 'motorcycle' : 'car' }}"></i>
                {{ $tipe->tipe_kendaraan }}
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th width="50">No</th>
                            <th>Durasi</th>
                            <th>Tarif</th>
                            <th width="150">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tipe->tarifParkir as $index => $tarif)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <strong>
                                    {{ $tarif->detailParkir->jam_min }} - {{ $tarif->detailParkir->jam_max }} jam
                                </strong>
                            </td>
                            <td class="fw-bold text-success">
                                Rp {{ number_format($tarif->tarif, 0, ',', '.') }}
                            </td>
                            <td>
                                <a href="{{ route('tarif-parkir.edit', $tarif->id_tarif) }}"
                                   class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('tarif-parkir.destroy', $tarif->id_tarif) }}"
                                      method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-danger btn-sm"
                                        onclick="return confirm('Hapus tarif ini?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-3 text-muted">
                                Belum ada tarif untuk tipe ini
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endsection